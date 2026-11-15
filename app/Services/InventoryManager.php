<?php

namespace App\Services;

use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\InventoryAdjustment;
use App\Models\InventoryReservation;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryManager
{
    public function assertProductCanBeDeleted(string $itemType, int $itemId): void
    {
        if (InventoryReservation::query()
            ->where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->whereIn('status', ['reserved', 'confirmed'])
            ->exists()) {
            throw ValidationException::withMessages(['product' => 'This item has an active stock reservation or booking and cannot be deleted. Cancel or fulfil its order first.']);
        }
    }

    public function reserveOrder(Order $order): void
    {
        $items = $order->items()->orderBy('item_type')->orderBy('item_id')->get();

        foreach ($items as $item) {
            $this->reserveItem($item);
        }
    }

    public function confirmOrder(Order $order): void
    {
        $reservations = InventoryReservation::query()
            ->whereHas('orderItem', fn ($query) => $query->where('order_id', $order->order_id))
            ->orderBy('item_type')->orderBy('item_id')->lockForUpdate()->get();

        foreach ($reservations as $reservation) {
            if ($reservation->status !== 'reserved') {
                continue;
            }

            $this->mutateReservationQuantity($reservation, 'confirm');
            $reservation->update(['status' => 'confirmed']);
        }
    }

    public function cancelOrder(Order $order): void
    {
        $reservations = InventoryReservation::query()
            ->whereHas('orderItem', fn ($query) => $query->where('order_id', $order->order_id))
            ->orderBy('item_type')->orderBy('item_id')->lockForUpdate()->get();

        foreach ($reservations as $reservation) {
            if ($reservation->status === 'reserved') {
                $this->mutateReservationQuantity($reservation, 'release');
                $reservation->update(['status' => 'released']);
            } elseif ($reservation->status === 'confirmed') {
                $this->mutateReservationQuantity($reservation, 'restore');
                $reservation->update(['status' => 'restored']);
            }
        }
    }

    public function fulfillOrder(Order $order): void
    {
        $reservations = InventoryReservation::query()
            ->whereHas('orderItem', fn ($query) => $query->where('order_id', $order->order_id))
            ->lockForUpdate()->get();

        if ($reservations->contains(fn (InventoryReservation $reservation) => $reservation->status === 'reserved')) {
            throw ValidationException::withMessages(['status' => 'Confirm the bank transfer before completing this order.']);
        }

        InventoryReservation::query()
            ->whereIn('id', $reservations->where('status', 'confirmed')->pluck('id'))
            ->update(['status' => 'fulfilled', 'updated_at' => now()]);
    }

    public function adjust(
        string $itemType,
        int $itemId,
        string $action,
        int $quantity,
        ?int $lowThreshold,
        ?string $productionType,
        ?string $note,
        User $actor
    ): void {
        DB::transaction(function () use ($itemType, $itemId, $action, $quantity, $lowThreshold, $productionType, $note, $actor): void {
            $record = $this->lockedRecord($itemType, $itemId);

            if ($itemType === 'event') {
                $this->adjustValue($record, 'booking_capacity', $record->reserved_slots + $record->booked_slots, $action, $quantity, 'capacity', $itemType, $itemId, $note, $actor);
                if ($lowThreshold !== null) {
                    $this->adjustValue($record, 'low_slots_threshold', 0, 'set', $lowThreshold, 'threshold', $itemType, $itemId, $note, $actor);
                }
            } elseif ($itemType === 'laser' && $productionType !== null && $productionType !== $record->production_type) {
                if ($record->reserved_quantity > 0 || InventoryReservation::query()
                    ->where('item_type', 'laser')->where('item_id', $itemId)->whereIn('status', ['reserved', 'confirmed'])->exists()) {
                    throw ValidationException::withMessages(['production_type' => 'Production type cannot change while stock is reserved or awaiting fulfilment.']);
                }
                $this->recordChange($itemType, $itemId, 'production_type', $record->production_type === 'ready_made' ? 1 : 0, $productionType === 'ready_made' ? 1 : 0, $note, $actor);
                $record->production_type = $productionType;
            }

            if ($itemType !== 'event' && ($itemType !== 'laser' || $record->production_type === 'ready_made')) {
                $this->adjustValue($record, 'stock_quantity', (int) $record->reserved_quantity, $action, $quantity, 'stock', $itemType, $itemId, $note, $actor);
                if ($lowThreshold !== null) {
                    $this->adjustValue($record, 'low_stock_threshold', 0, 'set', $lowThreshold, 'threshold', $itemType, $itemId, $note, $actor);
                }
            }

            $record->save();
        });
    }

    private function reserveItem(OrderItem $item): void
    {
        if (in_array($item->item_type, ['gift', 'frame'], true)) {
            $product = GiftDesign::query()->where('gift_design_id', $item->item_id)->lockForUpdate()->firstOrFail();
            $category = strtolower(trim($product->category));
            $categoryType = in_array($category, ['frame', 'frames'], true) ? 'frame' : 'gift';
            if ($categoryType !== $item->item_type) {
                throw ValidationException::withMessages(['items' => 'The selected product category is no longer valid.']);
            }
            $this->requireAvailable((int) $product->stock_quantity - (int) $product->reserved_quantity, $item->quantity);
            $product->increment('reserved_quantity', $item->quantity);
        } elseif ($item->item_type === 'laser') {
            $product = LaserWork::query()->where('laser_id', $item->item_id)->lockForUpdate()->firstOrFail();
            if ($product->production_type !== 'ready_made') {
                return;
            }
            $this->requireAvailable((int) $product->stock_quantity - (int) $product->reserved_quantity, $item->quantity);
            $product->increment('reserved_quantity', $item->quantity);
        } elseif ($item->item_type === 'event') {
            $event = Event::query()->where('event_id', $item->item_id)->lockForUpdate()->firstOrFail();
            $this->requireAvailable((int) $event->booking_capacity - (int) $event->reserved_slots - (int) $event->booked_slots, $item->quantity);
            $event->increment('reserved_slots', $item->quantity);
        } else {
            return;
        }

        InventoryReservation::create([
            'order_item_id' => $item->id,
            'item_type' => $item->item_type,
            'item_id' => $item->item_id,
            'quantity' => $item->quantity,
            'status' => 'reserved',
        ]);
    }

    private function mutateReservationQuantity(InventoryReservation $reservation, string $action): void
    {
        $field = $reservation->item_type === 'event' ? 'reserved_slots' : 'reserved_quantity';
        $model = match ($reservation->item_type) {
            'gift', 'frame' => GiftDesign::class,
            'laser' => LaserWork::class,
            'event' => Event::class,
            default => null,
        };
        if (! $model) {
            return;
        }

        $key = match ($reservation->item_type) {
            'gift', 'frame' => 'gift_design_id',
            'laser' => 'laser_id',
            'event' => 'event_id',
        };
        $record = $model::query()->where($key, $reservation->item_id)->lockForUpdate()->firstOrFail();
        $quantity = (int) $reservation->quantity;

        if ($action === 'confirm') {
            if ($reservation->item_type === 'event') {
                $record->decrement('reserved_slots', $quantity);
                $record->increment('booked_slots', $quantity);
            } else {
                $record->decrement('reserved_quantity', $quantity);
                $record->decrement('stock_quantity', $quantity);
            }
        } elseif ($action === 'release') {
            $record->decrement($field, $quantity);
        } elseif ($action === 'restore') {
            if ($reservation->item_type === 'event') {
                $record->decrement('booked_slots', $quantity);
            } else {
                $record->increment('stock_quantity', $quantity);
            }
        }
    }

    private function lockedRecord(string $type, int $id): GiftDesign|LaserWork|Event
    {
        if (in_array($type, ['gift', 'frame'], true)) {
            $query = GiftDesign::query()->where('gift_design_id', $id);
            $query->whereRaw(
                $type === 'frame' ? 'LOWER(category) IN (?, ?)' : 'LOWER(category) NOT IN (?, ?)',
                ['frame', 'frames']
            );

            return $query->lockForUpdate()->firstOrFail();
        }

        return match ($type) {
            'laser' => LaserWork::query()->lockForUpdate()->findOrFail($id),
            'event' => Event::query()->lockForUpdate()->findOrFail($id),
            default => throw ValidationException::withMessages(['item_type' => 'Select a valid inventory item.']),
        };
    }

    private function adjustValue(Model $record, string $field, int $minimum, string $action, int $quantity, string $type, string $itemType, int $itemId, ?string $note, User $actor): void
    {
        $before = (int) $record->{$field};
        $after = match ($action) {
            'add' => $before + $quantity,
            'remove' => $before - $quantity,
            'set' => $quantity,
            default => throw ValidationException::withMessages(['action' => 'Select a valid adjustment.']),
        };

        if ($after < $minimum || $after < 0 || $after > 2147483647) {
            throw ValidationException::withMessages(['quantity' => 'The adjustment would make stock lower than reserved quantities or exceed the supported range.']);
        }

        $record->{$field} = $after;
        $this->recordChange($itemType, $itemId, $type, $before, $after, $note, $actor);
    }

    private function recordChange(string $itemType, int $itemId, string $type, int $before, int $after, ?string $note, User $actor): void
    {
        InventoryAdjustment::create([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'adjustment_type' => $type,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'quantity_delta' => $after - $before,
            'note' => $note,
            'adjusted_by' => $actor->getKey(),
        ]);
    }

    private function requireAvailable(int $available, int $requested): void
    {
        if ($requested > $available) {
            throw ValidationException::withMessages(['items' => 'There is not enough available stock or event capacity for this quantity.']);
        }
    }
}
