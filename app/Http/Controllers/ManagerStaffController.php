<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManagerStaffController extends Controller
{
    public function index()
    {
        $members = Staff::query()
            ->where('role', 'staff')
            ->with('handles.service')
            ->withCount('handles')
            ->addSelect([
                'has_login_account' => User::query()
                    ->selectRaw('1')
                    ->whereColumn('users.email', 'staff.email')
                    ->limit(1),
            ])
            ->orderBy('full_name')
            ->get();

        return view('staff.staff.index', compact('members'));
    }

    public function create()
    {
        return view('staff.staff.form', ['member' => null, 'emailIsLogin' => false]);
    }

    public function store(Request $request)
    {
        $data = $this->validateMember($request);
        Staff::create($data + ['business_id' => null]);

        return redirect()->route('staff.staff.index')->with('success', 'Staff member added successfully.');
    }

    public function edit($id)
    {
        $member = Staff::findOrFail($id);
        abort_unless(strtolower($member->role) === 'staff', 403);

        return view('staff.staff.form', [
            'member' => $member,
            'emailIsLogin' => User::where('email', $member->email)->exists(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $member = Staff::findOrFail($id);
        abort_unless(strtolower($member->role) === 'staff', 403);
        $data = $this->validateMember($request, $member);
        $member->update($data);

        return redirect()->route('staff.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $member = Staff::findOrFail($id);

        abort_unless(strtolower($member->role) === 'staff', 403);

        if ($member->handles()->exists()) {
            return back()->withErrors([
                'staff' => 'This staff member has assignment history and cannot be deleted. Preserve the assignments and contact an administrator if the record must be changed.',
            ]);
        }

        if (User::where('email', $member->email)->exists()) {
            return back()->withErrors([
                'staff' => 'This staff member has a login account and cannot be deleted. Preserve the account and contact an administrator if it must be changed.',
            ]);
        }

        $member->delete();

        return redirect()->route('staff.staff.index')->with('success', 'Staff member deleted successfully.');
    }

    private function validateMember(Request $request, ?Staff $member = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            // Managers cannot create or promote anyone to an administrator.
            'role' => ['required', Rule::in(['staff'])],
            'email' => [
                'required',
                'email',
                Rule::unique('staff', 'email')->ignore($member?->staff_id, 'staff_id'),
                function (string $attribute, mixed $value, \Closure $fail) use ($member): void {
                    $email = strtolower(trim((string) $value));
                    $currentEmail = strtolower((string) $member?->email);

                    if ($member && $currentEmail !== '' && User::where('email', $member->email)->exists() && $email !== $currentEmail) {
                        $fail('A staff member email linked to a login account cannot be changed here.');

                        return;
                    }

                    if ($email !== $currentEmail && User::whereRaw('LOWER(email) = ?', [$email])->exists()) {
                        $fail('This email is already linked to an account and cannot be assigned to staff.');
                    }
                },
            ],
            'phone' => ['required', 'string', 'max:20'],
            'hire_date' => ['required', 'date'],
        ]);
    }
}
