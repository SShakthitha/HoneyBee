<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GiftDesign;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\GalleryImage;

class GiftDesignController extends Controller
{
    public function index()
    {
        $gifts = GiftDesign::query()
            ->whereRaw('LOWER(category) NOT IN (?, ?)', ['frame', 'frames'])
            ->get();
        $frames = GiftDesign::query()
            ->whereRaw('LOWER(category) IN (?, ?)', ['frame', 'frames'])
            ->get();
        // Keep the existing Gift & Design gallery as the gifts gallery so that
        // images uploaded before the category split remain visible.
        $giftGalleryImages = GalleryImage::where('category', 'Gift & Design')->get();
        $frameDesignImages = GalleryImage::where('category', 'Frame Designs')->get();

        return view('gift-design', compact(
            'gifts',
            'frames',
            'giftGalleryImages',
            'frameDesignImages'
        ));
    }

    public function show($id)
    {
        $gift = GiftDesign::findOrFail($id);
        return view('gift-design-detail', compact('gift'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
        ]);

        $gift = new GiftDesign();

        $gift->item_name = $request->item_name;
        $gift->category = $request->category;
        $gift->material = $request->material;
        $gift->size = $request->size;
        $gift->price = $request->price;
        $gift->offer_price = $request->offer_price ?? null;
        $gift->customization_option = $request->customization_option;
        $gift->description = $request->description;

        if ($request->hasFile('image')) {
            $gift->image = $request->file('image')->store('gifts', 'public');
        }

        $gift->save();

        return redirect('/admin/services')
            ->with('success', 'Gift added successfully!');
    }

    public function edit($id)
    {
        $gift = GiftDesign::findOrFail($id);
        return view('admin.gift.edit', compact('gift'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'item_name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
        ]);

        $gift = GiftDesign::findOrFail($id);

        $gift->item_name = $request->item_name;
        $gift->category = $request->category;
        $gift->material = $request->material;
        $gift->size = $request->size;
        $gift->price = $request->price;
        $gift->offer_price = $request->offer_price ?? null;
        $gift->customization_option = $request->customization_option;
        $gift->description = $request->description;

        // safer image update (prevents storage buildup)
        if ($request->hasFile('image')) {

            if ($gift->image && Storage::disk('public')->exists($gift->image)) {
                Storage::disk('public')->delete($gift->image);
            }

            $gift->image = $request->file('image')->store('gifts', 'public');
        }

        $gift->save();

        return redirect('/admin/services')
            ->with('success', 'Gift updated successfully!');
    }
}
