<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $promotions = Promotion::latest()->paginate(10);
        $viewer = $request->route()->getAction()['as'] ?? null;

        return view($viewer && str_starts_with($viewer, 'admin.') ? 'admin.promotions.index' : 'staff.promotions.index', compact('promotions'));
    }

    public function create(Request $request)
    {
        $viewer = $request->route()->getAction()['as'] ?? null;

        return view($viewer && str_starts_with($viewer, 'admin.') ? 'admin.promotions.form' : 'staff.promotions.form', ['promotion' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'button_text' => ['required', 'string', 'max:80'],
            'destination' => ['nullable', 'string', Rule::in(Promotion::destinationRoutes())],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['image'] = $request->file('image')?->store('promotions', 'public');

        Promotion::create($validated);

        return redirect()->route($request->route()->getName() === 'admin.promotions.store' ? 'admin.promotions.index' : 'staff.promotions.index')
            ->with('success', 'Promotion created successfully.');
    }

    public function edit(Promotion $promotion, Request $request)
    {
        $viewer = $request->route()->getAction()['as'] ?? null;

        return view($viewer && str_starts_with($viewer, 'admin.') ? 'admin.promotions.form' : 'staff.promotions.form', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'button_text' => ['required', 'string', 'max:80'],
            'destination' => ['nullable', 'string', Rule::in(Promotion::destinationRoutes())],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store('promotions', 'public');

            if ($promotion->image && Storage::disk('public')->exists($promotion->image)) {
                Storage::disk('public')->delete($promotion->image);
            }

            $validated['image'] = $newImage;
        }

        $promotion->update($validated);

        return redirect()->route($request->route()->getName() === 'admin.promotions.update' ? 'admin.promotions.index' : 'staff.promotions.index')
            ->with('success', 'Promotion updated successfully.');
    }

    public function destroy(Promotion $promotion, Request $request)
    {
        if ($promotion->image && Storage::disk('public')->exists($promotion->image)) {
            Storage::disk('public')->delete($promotion->image);
        }

        $promotion->delete();

        $routeName = $request->route()->getName();

        return redirect()->route(
            str_starts_with($routeName, 'admin.') ? 'admin.promotions.index' : 'staff.promotions.index'
        )->with('success', 'Promotion deleted successfully.');
    }
}
