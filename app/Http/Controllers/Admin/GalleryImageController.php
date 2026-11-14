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
        return view('admin.gallery.index', [
            'images' => GalleryImage::latest()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'in:Gift & Design,Frame Designs,Laser Work,Events'],
            'image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        if (! $path) {
            return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
        }

        GalleryImage::create([
            'title' => $validated['title'] ?? null,
            'category' => $validated['category'],
            'image' => $path,
        ]);

        return redirect()->route('admin.gallery.index')->with('success', 'Image uploaded successfully.');
    }
}
