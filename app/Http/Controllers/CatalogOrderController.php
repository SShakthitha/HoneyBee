<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InventoryManager;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CatalogOrderController extends Controller
{
    public function store(Request $request, InventoryManager $inventory): RedirectResponse
    {
        $data = $request->validate([
            'item_type' => ['required', 'in:gift,frame,laser,event'],
            'item_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'submission_token' => ['required', 'string', 'size:64', 'alpha_num'],
        ]);
        $customer = Customer::query()->where('email', $request->user()->email)->firstOrFail();

        $existing = Order::query()->where('customer_id', $customer->customer_id)
            ->where('submission_token', $data['submission_token'])->first();
        if ($existing) {
            return redirect()->route('checkout.confirmation', ['order' => $existing->order_id]);
        }

        $model = match ($data['item_type']) {
            'gift', 'frame' => GiftDesign::class,
            'laser' => LaserWork::class,
            'event' => Event::class,
        };
        $key = match ($data['item_type']) {
            'gift', 'frame' => 'gift_design_id',
            'laser' => 'laser_id',
            'event' => 'event_id',
        };
        $product = $model::query()->findOrFail($data['item_id']);

        $category = strtolower(trim((string) ($product->category ?? '')));
        $categoryType = in_array($category, ['frame', 'frames'], true) ? 'frame' : 'gift';
        if (in_array($data['item_type'], ['gift', 'frame'], true) && $categoryType !== $data['item_type']) {
            throw ValidationException::withMessages(['item_id' => 'The selected item does not match its product category.']);
        }

        $price = $this->cents($product->hasValidOfferPrice() ? $product->offer_price : $product->price);
        $quantity = (int) $data['quantity'];
        if ($price < 0 || $price > intdiv(9_999_999_999, $quantity)) {
            throw ValidationException::withMessages(['quantity' => 'The order quantity exceeds the supported order total.']);
        }
        $unitPrice = $this->money($price);
        $subtotal = $this->money($price * $quantity);

        try {
            $order = DB::transaction(function () use ($customer, $data, $product, $key, $quantity, $unitPrice, $subtotal, $inventory): Order {
                $order = Order::create([
                    'customer_id' => $customer->customer_id,
                    'submission_token' => $data['submission_token'],
                    'order_date' => now(),
                    'paid_amount' => $subtotal,
                    'advanced_paid' => 0,
                    'discount' => 0,
                    'payment_method' => 'bank_transfer',
                    'status' => 'pending',
                ]);
                $item = OrderItem::create([
                    'order_id' => $order->order_id,
                    'item_type' => $data['item_type'],
                    'item_id' => $product->{$key},
                    'item_name' => $this->name($product, $data['item_type']),
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
                $inventory->reserveOrder($order);
                $customer->increment('total_spent', $subtotal);

                return $order;
            });
        } catch (QueryException $exception) {
            $existing = Order::query()->where('customer_id', $customer->customer_id)
                ->where('submission_token', $data['submission_token'])->first();
            if (! $existing) {
                throw $exception;
            }
            $order = $existing;
        }

        return redirect()->route('checkout.confirmation', ['order' => $order->order_id]);
    }

    private function name(GiftDesign|LaserWork|Event $product, string $type): string
    {
        return match ($type) {
            'gift', 'frame' => $product->item_name,
            'laser' => $product->product_name,
            'event' => $product->event_name,
        };
    }

    private function cents(mixed $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function money(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
