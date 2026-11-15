<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        $galleryImages = GalleryImage::where('category','Events')->get();
        $submissionTokens = $events->mapWithKeys(fn (Event $event) => [$event->event_id => Str::random(64)]);
        $user = Auth::user();
        $isCustomer = $user
            && Customer::where('email', $user->email)->exists()
            && ! Staff::where('email', $user->email)->exists();

        return view('events', compact('events', 'galleryImages', 'submissionTokens', 'isCustomer'));
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('events', compact('event'));
    }
}
