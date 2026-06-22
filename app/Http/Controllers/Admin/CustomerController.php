<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('admin.customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
        ]);

        Customer::create([
            'full_name'       => $request->full_name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'total_spent'     => 0,
            'registered_date' => now(),
        ]);

        return back()->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email,' . $id . ',customer_id',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
        ]);

        $customer->update([
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
        ]);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return back()->with('success', 'Customer deleted.');
    }
}