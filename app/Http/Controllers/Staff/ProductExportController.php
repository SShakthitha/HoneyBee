<?php

namespace App\Http\Controllers\Staff;

use App\Exports\HoneyBeeExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ProductExportController extends Controller
{
    public function __invoke(): Response
    {
        $products = collect()
            ->merge(GiftDesign::orderBy('gift_design_id')->get()->map(fn (GiftDesign $item) => $this->row('Gift & Design', $item->gift_design_id, $item->item_name, $item->category, $item->material, $item->size, $item)))
            ->merge(LaserWork::orderBy('laser_id')->get()->map(fn (LaserWork $item) => $this->row('Laser Work', $item->laser_id, $item->product_name, $item->product_category ?: $item->laser_type, $item->material_type, $item->size, $item)))
            ->merge(Event::orderBy('event_id')->get()->map(fn (Event $item) => $this->row('Event', $item->event_id, $item->event_name, $item->event_type, null, null, $item)));

        return Excel::download(new HoneyBeeExport(
            $products,
            ['Type', 'Product ID', 'Name', 'Category', 'Material', 'Size', 'Original Price', 'Offer Price', 'Description', 'Created Date'],
            fn (array $row) => $row
        ), 'honeybee-staff-products.xlsx');
    }

    private function row(string $type, int $id, string $name, ?string $category, ?string $material, ?string $size, object $item): array
    {
        return [$type, $id, $name, $category, $material, $size, $item->price, $item->offer_price, $item->description, $item->created_at?->format('Y-m-d')];
    }
}
