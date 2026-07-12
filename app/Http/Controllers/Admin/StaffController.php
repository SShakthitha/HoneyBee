<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
  public function data()
{
    $staff = Staff::select('staff_id', 'full_name', 'role', 'email', 'phone', 'hire_date');

    return DataTables::of($staff)
        ->editColumn('hire_date', function ($s) {
            return $s->hire_date ? \Carbon\Carbon::parse($s->hire_date)->format('Y-m-d') : '—';
        })
        ->addColumn('action', function ($s) {
            return '
                <div class="d-flex justify-content-end gap-1">
                    <button type="button"
                        class="btn btn-honey btn-sm editBtn"
                        data-id="' . $s->staff_id . '">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button type="button"
                        class="btn btn-outline-danger btn-sm deleteBtn"
                        data-id="' . $s->staff_id . '">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
}

// AJAX GET — returns JSON to populate the edit modal
public function edit($id)
{
    $member = Staff::where('staff_id', $id)->firstOrFail();
    return response()->json($member);
}
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