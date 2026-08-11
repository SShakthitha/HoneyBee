<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->where('availability_status', 'available')->paginate(12);

        return view('home', compact('services'));
    }

    public function show(int $id)
    {
        $service = Service::findOrFail($id);

        return view('order-create', compact('service'));
    }
}
