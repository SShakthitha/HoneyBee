<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderStatusManager
{
    public function update(Order $order, string $status, InventoryManager $inventory): void
    {
        validator(['status' => $status], [
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
        ])->validate();

        DB::transaction(function () use ($order, $status, $inventory): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->order_id);

            if ($lockedOrder->status === 'cancelled' && $status !== 'cancelled') {
                throw ValidationException::withMessages(['status' => 'A cancelled order cannot be reopened. Place a new order instead.']);
            }
            if ($lockedOrder->status === 'completed' && ! in_array($status, ['completed', 'cancelled'], true)) {
                throw ValidationException::withMessages(['status' => 'A completed order cannot return to an earlier status.']);
            }

            if ($status === 'cancelled' && $lockedOrder->status !== 'cancelled') {
                $inventory->cancelOrder($lockedOrder);
            } elseif ($status === 'completed' && $lockedOrder->status !== 'completed') {
                $inventory->fulfillOrder($lockedOrder);
            }

            $lockedOrder->status = $status;
            $lockedOrder->save();
        });
    }
}
