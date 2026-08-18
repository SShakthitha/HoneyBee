<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\GalleryImage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('availability_status', 'available')
            ->paginate(12);

        $galleryImages = GalleryImage::latest()->get();

        return view('home', compact('services', 'galleryImages'));
    }
}