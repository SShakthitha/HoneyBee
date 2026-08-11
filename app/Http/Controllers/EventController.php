<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GalleryImage;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        $galleryImages = GalleryImage::where('category','Events')->get();
        return view('events', compact('events', 'galleryImages'));
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('events', compact('event'));
    }
}
