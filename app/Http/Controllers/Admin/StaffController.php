<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::all();
        return view('admin.staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'role'      => 'required|string|max:255',
            'email'     => 'required|email|unique:staff,email',
            'phone'     => 'required|string|max:20',
            'hire_date' => 'required|date',
        ]);

        Staff::create([
            'business_id' => null,
            'full_name'   => $request->full_name,
            'role'        => $request->role,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'hire_date'   => $request->hire_date,
        ]);

        return back()->with('success', 'Staff member added successfully.');
    }

    public function update(Request $request, $id)
    {
        $member = Staff::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'role'      => 'required|string|max:255',
            'email'     => 'required|email|unique:staff,email,' . $id . ',staff_id',
            'phone'     => 'required|string|max:20',
            'hire_date' => 'required|date',
        ]);

        $member->update([
            'full_name' => $request->full_name,
            'role'      => $request->role,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'hire_date' => $request->hire_date,
        ]);

        return back()->with('success', 'Staff member updated successfully.');
    }

    public function destroy($id)
    {
        Staff::findOrFail($id)->delete();
        return back()->with('success', 'Staff member deleted.');
    }
}