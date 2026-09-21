<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManagerStaffController extends Controller
{
    public function index()
    {
        return view('staff.staff.index', ['members' => Staff::orderBy('full_name')->get()]);
    }

    public function create()
    {
        return view('staff.staff.form', ['member' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validateMember($request);
        Staff::create($data + ['business_id' => null]);

        return redirect()->route('staff.staff.index')->with('success', 'Staff member added successfully.');
    }

    public function edit($id)
    {
        return view('staff.staff.form', ['member' => Staff::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $member = Staff::findOrFail($id);
        if ($this->isCurrentManager($request, $member)) {
            abort_unless($request->input('role') === $member->role && $request->input('email') === $member->email, 403);
        }
        $data = $this->validateMember($request, $member);

        if ($this->isCurrentManager($request, $member)) {
            // A manager may maintain their own contact details, but not their
            // identity or authorisation role through staff management.
            $member->update(collect($data)->only(['full_name', 'phone'])->all());
        } else {
            $member->update($data);
        }

        return redirect()->route('staff.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $member = Staff::findOrFail($id);
        abort_if($this->isCurrentManager($request, $member), 403, 'Managers cannot delete their own account.');
        $member->delete();

        return redirect()->route('staff.staff.index')->with('success', 'Staff member deleted successfully.');
    }

    private function validateMember(Request $request, ?Staff $member = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            // Managers cannot create or promote anyone to an administrator.
            'role' => ['required', Rule::in(['staff', 'manager'])],
            'email' => ['required', 'email', Rule::unique('staff', 'email')->ignore($member?->staff_id, 'staff_id')],
            'phone' => ['required', 'string', 'max:20'],
            'hire_date' => ['required', 'date'],
        ]);
    }

    private function isCurrentManager(Request $request, Staff $member): bool
    {
        return $member->email === $request->user()->email;
    }
}
