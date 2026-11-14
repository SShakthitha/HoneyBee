<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        $customer = Customer::where('email', $user->email)->firstOrFail();

        return view('customer.edit-profile', compact('customer'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $customer = Customer::where('email', $user->email)->first();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($customer, $request): void {
            $customer->update([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profile updated successfully!');
    }
}
