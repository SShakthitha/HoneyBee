<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->with('business')
            ->where('availability_status', 'available')
            ->latest('service_id')
            ->paginate(12);

        return view('services.index', compact('services'));
    }

    public function show(int $id): View
    {
        $service = Service::query()
            ->with('business')
            ->where('availability_status', 'available')
            ->findOrFail($id);

        return view('services.show', compact('service'));
    }
}
