<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderPaymentRecorder
{
    /** Record an advance only after an authorized staff member confirms receipt. */
    public function recordConfirmedAdvance(Order $order, string $amount): void
    {
        $advanceCents = $this->amountInCents($amount);

        DB::transaction(function () use ($order, $advanceCents): void {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->order_id);

            if ($advanceCents > $this->amountInCents($lockedOrder->paid_amount)) {
                throw ValidationException::withMessages([
                    'advanced_paid' => 'The confirmed advance cannot exceed the order total.',
                ]);
            }

            $lockedOrder->advanced_paid = $this->centsToAmount($advanceCents);
            $lockedOrder->payment_date = $advanceCents > 0 ? now() : null;
            $lockedOrder->save();
        });
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
}
