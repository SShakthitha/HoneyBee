<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryImageController extends Controller
{
    public function index()
    {
        $images = GalleryImage::latest()->get();

        return view('admin.gallery.index', compact('images'));
    }


    public function create()
    {
        return view('admin.gallery.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',

            'category' => [
                'required',
                'in:Gift & Design,Frame Designs,Laser Work,Events'
            ],

            'image' => [
                'required',
                'image',
                'max:2048'
            ]
        ]);


        $path = $request->file('image')
                        ->store('gallery', 'public');


        GalleryImage::create([
            'title' => $request->title,
            'category' => $request->category,
            'image' => $path,
        ]);


        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Image uploaded successfully.');
    }

    public function destroy($id)
    {
        $image = GalleryImage::findOrFail($id);

        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Image deleted successfully.');
    }
}
