<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return view('events', compact('events'));
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('event-detail', compact('event'));
    }
}