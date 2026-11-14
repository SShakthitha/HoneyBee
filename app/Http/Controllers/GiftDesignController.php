<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\GiftDesign;
use App\Rules\ValidGiftImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GiftDesignController extends Controller
{
    public function sendEnquiry(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'product' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $lines = [
            'Gift & Design enquiry',
            'Name: '.$data['name'],
            'Phone: '.$data['phone'],
            'Email: '.($data['email'] ?? 'Not provided'),
            'Product: '.$data['product'],
            'Message: '.($data['message'] ?? 'No additional message'),
        ];
        $url = 'https://wa.me/'.config('honeybee.contact.whatsapp_number').'?text='.rawurlencode(implode("\n", $lines));

        return redirect()->away($url);
    }

    public function index()
    {
        $gifts = GiftDesign::query()
            ->where(function ($query) {
                $query->whereNull('category')
                    ->orWhereRaw('LOWER(TRIM(category)) NOT IN (?, ?, ?, ?, ?, ?, ?, ?)', [
                        'frame',
                        'frames',
                        'frame design',
                        'frame designs',
                        'frame-design',
                        'frame_design',
                        'frame designs',
                        'frames design',
                    ]);
            })
            ->get();

        $frames = GiftDesign::query()
            ->whereRaw('LOWER(TRIM(category)) IN (?, ?, ?, ?, ?, ?, ?, ?)', [
                'frame',
                'frames',
                'frame design',
                'frame designs',
                'frame-design',
                'frame_design',
                'frame designs',
                'frames design',
            ])
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

        return view('gift-design-detail', ['gift' => $gift, 'submissionToken' => Str::random(64)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
            'image' => ['nullable', 'file', 'max:10240', new ValidGiftImage],
        ]);

        $gift = new GiftDesign;

        $gift->item_name = $request->item_name;
        $gift->category = $request->category;
        $gift->material = $request->material;
        $gift->size = $request->size;
        $gift->price = $request->price;
        $gift->offer_price = $request->offer_price ?? null;
        $gift->customization_option = $request->customization_option;
        $gift->description = $request->description;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('gifts', 'public');

            if (! $imagePath) {
                return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
            }

            $gift->image = $imagePath;
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
            'image' => ['nullable', 'file', 'max:10240', new ValidGiftImage],
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

        $oldImagePath = $gift->image;
        $newImagePath = null;

        if ($request->hasFile('image')) {

            $newImagePath = $request->file('image')->store('gifts', 'public');

            if (! $newImagePath) {
                return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
            }

            $gift->image = $newImagePath;
        }

        $gift->save();

        if ($newImagePath && $oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect('/admin/services')
            ->with('success', 'Gift updated successfully!');
    }
}
