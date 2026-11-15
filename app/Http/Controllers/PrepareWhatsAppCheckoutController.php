<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GiftDesign;
use App\Services\WhatsAppOrderMessageBuilder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PrepareWhatsAppCheckoutController extends Controller
{
    public function __invoke(Request $request, WhatsAppOrderMessageBuilder $messageBuilder)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.item_id' => ['required', 'integer', 'min:1'],
            'items.*.item_type' => ['required', 'in:gift,frame'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $customer = Customer::query()->where('email', $request->user()->email)->first();
        abort_unless($customer, 403, 'A customer profile is required to prepare an order request.');

        $items = collect($validated['items'])->map(function (array $item): array {
            $product = GiftDesign::query()->find($item['item_id']);
            if (! $product) {
                throw ValidationException::withMessages([
                    'items' => 'A selected product is no longer available. Please review your cart.',
                ]);
            }

            $category = strtolower(trim($product->category));
            $actualType = in_array($category, ['frame', 'frames'], true) ? 'frame' : 'gift';
            if ($actualType !== $item['item_type']) {
                throw ValidationException::withMessages([
                    'items' => 'A selected product changed category. Please review your cart.',
                ]);
            }

            $price = $product->hasValidOfferPrice() ? $product->offer_price : $product->price;
            $priceCents = $this->amountInCents($price);
            $quantity = (int) $item['quantity'];
            if ($priceCents < 0 || $priceCents > intdiv(9_999_999_999, $quantity)) {
                throw ValidationException::withMessages([
                    'items' => 'An item quantity exceeds the supported request total.',
                ]);
            }

            return [
                'item_id' => (int) $product->gift_design_id,
                'item_type' => $actualType,
                'item_name' => $product->item_name,
                'price' => $this->centsToAmount($priceCents),
                'quantity' => $quantity,
                'subtotal' => $this->centsToAmount($priceCents * $quantity),
                'subtotal_cents' => $priceCents * $quantity,
            ];
        });

        $totalCents = $items->sum('subtotal_cents');
        if ($totalCents > 9_999_999_999) {
            throw ValidationException::withMessages([
                'items' => 'The request total exceeds the supported amount.',
            ]);
        }

        $whatsAppUrl = $messageBuilder->urlForCheckoutRequest(
            $customer,
            $items,
            trim((string) ($validated['notes'] ?? '')),
            $this->centsToAmount($totalCents),
        );

        if (! $whatsAppUrl) {
            return back()->withInput()->withErrors([
                'whatsapp' => 'WhatsApp is unavailable right now. Please contact HoneyBee using the contact details on this website.',
            ]);
        }

        return response()->view('checkout-whatsapp-redirect', compact('whatsAppUrl'));
    }

    private function amountInCents(mixed $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0', STR_PAD_RIGHT);
    }

    private function centsToAmount(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }
}
