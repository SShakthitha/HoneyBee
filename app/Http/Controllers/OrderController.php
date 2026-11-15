<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GiftDesign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Services\InventoryManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $customer = Customer::where('email', auth()->user()->email)
            ->firstOrFail();

        $orders = Order::with('items')
            ->where('customer_id', $customer->customer_id)
            ->latest('order_date')
            ->get();

        return view('orders', compact('orders'));
    }

    public function show($id)
    {
        $customer = Customer::where('email', auth()->user()->email)
            ->firstOrFail();

        $order = Order::with('items')
            ->where('customer_id', $customer->customer_id)
            ->where('order_id', $id)
            ->firstOrFail();

        return view('order-show', compact('order'));
    }

    public function downloadPdf(Request $request, $id)
    {
        $customer = Customer::where('email', $request->user()->email)->firstOrFail();

        $order = Order::with(['customer', 'items'])
            ->where('customer_id', $customer->customer_id)
            ->where('order_id', $id)
            ->firstOrFail();

        return $this->orderPdf($order);
    }

    private function orderPdf(Order $order)
    {
        $logoPath = public_path('images/logo.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        return Pdf::loadView('pdf.order-summary', compact('order', 'logo'))
            ->setPaper('a4')
            ->download('honeybee-order-'.$order->order_id.'.pdf');
    }

    public function create(Service $service)
    {
        abort_unless($service->availability_status === 'available', 404);

        return view('order-create', [
            'service' => $service,
            'submissionToken' => Str::random(64),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Existing service order
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,service_id'],
            'submission_token' => ['required', 'string', 'size:64', 'alpha_num'],
            'payment_method' => ['required', Rule::in(['bank_transfer'])],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'attribute' => ['nullable', 'string', 'max:5000'],
        ]);

        $service = Service::query()
            ->where('availability_status', 'available')
            ->findOrFail($validated['service_id']);

        $totalCents = $this->amountInCents($service->price);

        $customer = Customer::where('email', $request->user()->email)
            ->firstOrFail();

        if ($this->orderForSubmission($customer, $validated['submission_token'])) {
            return redirect('/orders')->with('success', 'This order was already received.');
        }

        $total = $this->centsToAmount($totalCents);
        try {
            DB::transaction(function () use ($customer, $service, $validated, $total): void {
                Order::create([
                    'customer_id' => $customer->customer_id,
                    'submission_token' => $validated['submission_token'],
                    'service_id' => $service->service_id,
                    'order_date' => now(),
                    'paid_amount' => $total,
                    'advanced_paid' => '0.00',
                    'discount' => '0.00',
                    'payment_method' => $validated['payment_method'],
                    'status' => 'pending',
                    'delivery_date' => $validated['delivery_date'] ?? null,
                    'attribute' => $validated['attribute'] ?? null,
                ]);

                $customer->increment('total_spent', $total);
            });
        } catch (QueryException $exception) {
            if (! $this->orderForSubmission($customer, $validated['submission_token'])) {
                throw $exception;
            }
        }

        return redirect('/orders')
            ->with('success', 'Order placed successfully!');
    }

    private function amountInCents(mixed $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function centsToAmount(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Gift & Design Cart Order
    |--------------------------------------------------------------------------
    */

    public function storeCartOrder(Request $request, InventoryManager $inventory)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'submission_token' => ['required', 'string', 'size:64', 'alpha_num'],
            'items.*.quantity' => 'required|integer|min:1|max:2147483647',
            'items.*.item_id' => 'required|integer',
            'items.*.item_type' => 'required|in:gift,frame',
            'notes' => 'nullable|string|max:5000',
        ]);

        $customer = Customer::where('email', $request->user()->email)
            ->firstOrFail();

        if ($existingOrder = $this->orderForSubmission($customer, $validated['submission_token'])) {
            return redirect()->route('checkout.confirmation', ['order' => $existingOrder->order_id]);
        }

        $items = collect($validated['items'])->map(function (array $item) {
            $product = GiftDesign::findOrFail($item['item_id']);

            $category = strtolower(trim($product->category));
            $categoryType = in_array($category, ['frame', 'frames'], true) ? 'frame' : 'gift';
            abort_unless($categoryType === $item['item_type'], 422);

            // Prices from the browser are never trusted. Use the product's
            // centralized offer-price rule and keep all arithmetic in cents.
            $priceCents = $this->amountInCents(
                $product->hasValidOfferPrice() ? $product->offer_price : $product->price
            );
            $quantity = (int) $item['quantity'];
            if ($priceCents < 0 || $priceCents > intdiv(9_999_999_999, $quantity)) {
                throw ValidationException::withMessages(['items' => 'An item quantity exceeds the supported order total.']);
            }

            return [
                'item_id' => $product->gift_design_id,
                'item_type' => $item['item_type'],
                'item_name' => $product->item_name,
                'price' => $this->centsToAmount($priceCents),
                'quantity' => $quantity,
                'subtotal' => $this->centsToAmount($priceCents * $quantity),
                'subtotal_cents' => $priceCents * $quantity,
            ];
        });

        $totalCents = $items->sum('subtotal_cents');
        if ($totalCents > 9_999_999_999) {
            throw ValidationException::withMessages(['items' => 'The order total exceeds the supported amount.']);
        }
        $total = $this->centsToAmount($totalCents);

        try {
            $order = DB::transaction(function () use (
                $items,
                $customer,
                $total,
                $validated,
                $inventory
            ) {
                $order = Order::create([
                    'customer_id' => $customer->customer_id,
                    'submission_token' => $validated['submission_token'],
                    'order_date' => now(),
                    'paid_amount' => $total,
                    'advanced_paid' => 0,
                    'discount' => 0,
                    'payment_method' => 'bank_transfer',
                    'status' => 'pending',
                    'attribute' => $validated['notes'] ?? null,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'item_type' => $item['item_type'],
                        'item_id' => $item['item_id'],
                        'item_name' => $item['item_name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                $inventory->reserveOrder($order);

                $customer->increment('total_spent', $total);

                return $order;
            });
        } catch (QueryException $exception) {
            $existingOrder = $this->orderForSubmission($customer, $validated['submission_token']);
            if (! $existingOrder) {
                throw $exception;
            }

            return redirect()->route('checkout.confirmation', ['order' => $existingOrder->order_id]);
        }

        return redirect()
            ->route('checkout.confirmation', ['order' => $order->order_id]);
    }

    private function orderForSubmission(Customer $customer, string $token): ?Order
    {
        return Order::query()
            ->where('customer_id', $customer->customer_id)
            ->where('submission_token', $token)
            ->first();
    }
}
