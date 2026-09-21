<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HoneyBeeExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\Staff;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function customers(): BinaryFileResponse
    {
        return Excel::download(new HoneyBeeExport(
            Customer::orderBy('customer_id')->get(),
            ['Customer ID', 'Name', 'Email', 'Phone', 'Address', 'Registration Date', 'Total Spent'],
            fn (Customer $customer) => [$customer->customer_id, $customer->full_name, $customer->email, $customer->phone, $customer->address, $this->date($customer->registered_date), $customer->total_spent]
        ), 'honeybee-customers.xlsx');
    }

    public function products(): BinaryFileResponse
    {
        $products = collect()
            ->merge(GiftDesign::orderBy('gift_design_id')->get()->map(fn (GiftDesign $item) => $this->productRow('Gift & Design', $item->gift_design_id, $item->item_name, $item->category, $item->material, $item->size, $item)))
            ->merge(LaserWork::orderBy('laser_id')->get()->map(fn (LaserWork $item) => $this->productRow('Laser Work', $item->laser_id, $item->product_name, $item->product_category ?: $item->laser_type, $item->material_type, $item->size, $item)))
            ->merge(Event::orderBy('event_id')->get()->map(fn (Event $item) => $this->productRow('Event', $item->event_id, $item->event_name, $item->event_type, null, null, $item)));

        return Excel::download(new HoneyBeeExport(
            $products,
            ['Type', 'Product ID', 'Name', 'Category', 'Material', 'Size', 'Original Price', 'Offer Price', 'Description', 'Created Date'],
            fn (array $row) => $row
        ), 'honeybee-products.xlsx');
    }

    public function orders(): BinaryFileResponse
    {
        $rows = Order::with(['customer', 'items'])->orderByDesc('order_date')->get()
            ->flatMap(fn (Order $order) => $order->items->map(fn ($item) => [
                $order->order_id,
                $order->customer?->full_name ?: 'Unknown customer',
                $order->customer?->email,
                $this->date($order->order_date),
                $item->item_name,
                $item->item_type,
                $item->quantity,
                $item->price,
                $item->subtotal,
                $order->status,
            ]));

        return Excel::download(new HoneyBeeExport(
            $rows,
            ['Order ID', 'Customer', 'Customer Email', 'Order Date', 'Product / Service', 'Product Type', 'Quantity', 'Unit Price', 'Line Total', 'Order Status'],
            fn (array $row) => $row
        ), 'honeybee-orders.xlsx');
    }

    public function staff(): BinaryFileResponse
    {
        return Excel::download(new HoneyBeeExport(
            Staff::orderBy('staff_id')->get(),
            ['Staff ID', 'Name', 'Role', 'Email', 'Phone', 'Hire Date'],
            fn (Staff $staff) => [$staff->staff_id, $staff->full_name, $staff->role, $staff->email, $staff->phone, $this->date($staff->hire_date)]
        ), 'honeybee-staff.xlsx');
    }

    private function productRow(string $type, int $id, string $name, ?string $category, ?string $material, ?string $size, object $item): array
    {
        return [$type, $id, $name, $category, $material, $size, $item->price, $item->offer_price, $item->description, $this->date($item->created_at)];
    }

    private function date(mixed $value): ?string
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}
