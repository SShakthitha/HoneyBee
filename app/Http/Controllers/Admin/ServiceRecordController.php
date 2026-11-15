<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceRecordController extends Controller
{
    public function index(): View
    {
        $services = Service::with('business')->latest('service_id')->paginate(20);

        return view('admin.service-records.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.service-records.create', ['businesses' => Business::orderBy('business_name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Service::create($this->validatedData($request));

        return redirect()->route('admin.service-records.index')->with('success', 'Service created successfully.');
    }

    public function edit(int $id): View
    {
        $service = Service::findOrFail($id);

        return view('admin.service-records.edit', [
            'service' => $service,
            'businesses' => Business::orderBy('business_name')->get(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        $service->update($this->validatedData($request));

        return redirect()->route('admin.service-records.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);

        if ($service->orders()->exists() || $service->handles()->exists()) {
            return back()->withErrors(['service' => 'This service has order or staff history and cannot be deleted. Deactivate it instead.']);
        }

        $service->delete();

        return redirect()->route('admin.service-records.index')->with('success', 'Service deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,business_id'],
            'service_name' => ['required', 'string', 'max:255'],
            'service_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'availability_status' => ['required', 'in:available,unavailable'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'category' => ['nullable', 'string', 'max:255'],
            'served_date' => ['nullable', 'date'],
        ]);
    }
}
