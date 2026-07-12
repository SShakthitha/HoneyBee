<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customers.index');
    }

    public function data()
    {
        $customers = Customer::select('customer_id', 'full_name', 'email', 'phone', 'address');

        return DataTables::of($customers)
            ->addColumn('action', function ($customer) {
                return '
                    <div class="d-flex justify-content-end gap-1">
                        <button type="button"
                            class="btn btn-honey btn-sm editBtn"
                            data-id="' . $customer->customer_id . '">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button"
                            class="btn btn-outline-danger btn-sm deleteBtn"
                            data-id="' . $customer->customer_id . '">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer added successfully.');
    }

    // AJAX GET — returns JSON to populate the edit modal
    public function edit($id)
    {
        $customer = Customer::where('customer_id', $id)->firstOrFail();

        return response()->json($customer);
    }

    // Normal PUT form submission — full page redirect, same as store()
    public function update(Request $request, $id)
    {
        $customer = Customer::where('customer_id', $id)->firstOrFail();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        Customer::where('customer_id', $id)->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}