<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\InventoryAdjustment;
use App\Models\LaserWork;
use App\Models\Staff;
use App\Models\User;
use App\Services\InventoryManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $items = collect();

        foreach (GiftDesign::query()->when($search !== '', fn ($query) => $query->where('item_name', 'like', '%'.$search.'%'))->get() as $item) {
            $type = in_array(strtolower(trim($item->category)), ['frame', 'frames'], true) ? 'frame' : 'gift';
            $items->push($this->row($type, $item->gift_design_id, $item->item_name.' ('.strtolower($item->category).')', $item->stock_quantity, $item->reserved_quantity, $item->available_stock, $item->low_stock_threshold));
        }

        foreach (LaserWork::query()->when($search !== '', fn ($query) => $query->where('product_name', 'like', '%'.$search.'%'))->get() as $item) {
            $items->push($this->row('laser', $item->laser_id, $item->product_name, $item->stock_quantity, $item->reserved_quantity, $item->available_stock, $item->low_stock_threshold, $item->production_type));
        }

        foreach (Event::query()->when($search !== '', fn ($query) => $query->where('event_name', 'like', '%'.$search.'%'))->get() as $item) {
            $items->push($this->row('event', $item->event_id, $item->event_name, $item->booking_capacity, $item->reserved_slots, $item->available_slots, $item->low_slots_threshold, null, $item->booked_slots));
        }

        $history = InventoryAdjustment::query()->with('adjustedBy')->latest()->limit(100)->get();

        $isAdminInventoryRoute = $request->routeIs('admin.inventory.*');

        return view('inventory.index', [
            'items' => $items->sortBy('name')->values(),
            'history' => $history,
            'search' => $search,
            'canAdjust' => $this->canAdjust($request->user()),
            'layout' => $isAdminInventoryRoute ? 'layouts.admin' : 'layouts.staff',
            'adjustRoute' => $isAdminInventoryRoute ? 'admin.inventory.adjust' : 'manager.inventory.adjust',
        ]);
    }

    public function adjust(Request $request, string $type, int $id, InventoryManager $inventory): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:add,remove,set'],
            'quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:2147483647'],
            'production_type' => ['nullable', 'in:ready_made,custom_made'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);
        if ($data['action'] !== 'set' && (int) $data['quantity'] < 1) {
            return back()->withErrors(['quantity' => 'Add and remove adjustments must be at least one.'])->withInput();
        }

        $inventory->adjust(
            $type,
            $id,
            $data['action'],
            (int) $data['quantity'],
            isset($data['low_stock_threshold']) ? (int) $data['low_stock_threshold'] : null,
            $data['production_type'] ?? null,
            $data['note'] ?? null,
            $request->user(),
        );

        return back()->with('success', 'Inventory updated successfully.');
    }

    private function row(string $type, int $id, string $name, int $physical, int $reserved, int $available, int $threshold, ?string $productionType = null, int $booked = 0): object
    {
        return (object) compact('type', 'id', 'name', 'physical', 'reserved', 'available', 'threshold', 'productionType', 'booked');
    }

    private function canAdjust(User $user): bool
    {
        return Staff::query()
            ->where('email', $user->email)
            ->whereRaw('LOWER(TRIM(role)) IN (?, ?)', ['admin', 'manager'])
            ->exists();
    }
}
