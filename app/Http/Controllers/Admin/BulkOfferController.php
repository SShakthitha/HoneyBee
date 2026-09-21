<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BulkOfferController extends Controller
{
    public function apply(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request, false);

        if ($data['offer_type'] === 'percentage' && $data['offer_value'] >= 100) {
            throw ValidationException::withMessages(['offer_value' => 'The percentage discount must be less than 100.']);
        }

        $items = $this->items($data);

        if ($data['offer_type'] === 'fixed' && $items->contains(fn ($item) => $data['offer_value'] >= $item->price)) {
            throw ValidationException::withMessages(['offer_value' => 'The fixed discount must be lower than every selected product price.']);
        }

        DB::transaction(function () use ($items, $data) {
            $items->each(function ($item) use ($data) {
                $offerPrice = $data['offer_type'] === 'percentage'
                    ? round($item->price * (1 - ($data['offer_value'] / 100)), 2)
                    : round($item->price - $data['offer_value'], 2);

                $item->update(['offer_price' => $offerPrice]);
            });
        });

        return back()->with('success', "Offer applied to {$items->count()} product(s).");
    }

    public function remove(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request, true);
        $items = $this->items($data);
        $items->each->update(['offer_price' => null]);

        return back()->with('success', "Offer removed from {$items->count()} product(s).");
    }

    private function validateRequest(Request $request, bool $removing): array
    {
        return $request->validate([
            'product_type' => ['required', Rule::in(['gift', 'laser', 'event'])],
            'scope' => ['required', Rule::in(['selected', 'category'])],
            'product_ids' => ['required_if:scope,selected', 'array', 'min:1'],
            'product_ids.*' => ['integer'],
            'category' => ['required_if:scope,category', 'nullable', 'string', 'max:255'],
            'offer_type' => [$removing ? 'nullable' : 'required', Rule::in(['percentage', 'fixed'])],
            'offer_value' => [$removing ? 'nullable' : 'required', 'numeric', 'gt:0'],
        ]);
    }

    private function items(array $data)
    {
        $query = $this->queryFor($data['product_type']);

        if ($data['scope'] === 'category') {
            $column = $data['product_type'] === 'gift' ? 'category' : ($data['product_type'] === 'laser' ? 'product_category' : 'event_type');
            $query->where($column, $data['category']);
        } else {
            $query->whereKey($data['product_ids']);
        }

        $items = $query->get();

        if ($items->isEmpty() || ($data['scope'] === 'selected' && $items->count() !== count(array_unique($data['product_ids'])))) {
            throw ValidationException::withMessages(['product_ids' => 'Select valid existing products to continue.']);
        }

        return $items;
    }

    private function queryFor(string $type): Builder
    {
        return match ($type) {
            'gift' => GiftDesign::query(),
            'laser' => LaserWork::query(),
            'event' => Event::query(),
        };
    }
}
