<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffInvitationController extends Controller
{
    public function create(string $token): View
    {
        $invitation = $this->findInvitation($token);
        $problem = $this->problem($invitation);

        return view('staff.invitation-register', [
            'invitation' => $invitation,
            'problem' => $problem,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $user = DB::transaction(function () use ($request, $token) {
            $invitation = StaffInvitation::where('token_hash', hash('sha256', $token))->lockForUpdate()->first();
            abort_if($this->problem($invitation) !== null, 410, $this->problem($invitation));

            $staff = Staff::where('staff_id', $invitation->staff_id)->lockForUpdate()->firstOrFail();
            abort_unless(strtolower(trim($staff->role)) === 'staff', 410, 'This staff record is not eligible for registration. Contact Admin.');
            abort_if(User::where('email', $staff->email)->exists(), 409, 'An account already exists for this staff member. Contact Admin.');

            $user = User::create([
                'name' => $staff->full_name,
                'email' => $staff->email,
                'password' => Hash::make($request->password),
            ]);
            // email_verified_at is intentionally not mass assignable on User.
            // Set it explicitly so invited accounts can pass the existing
            // `verified` middleware used by the Staff Dashboard.
            $user->email_verified_at = now();
            $user->save();
            $invitation->update(['accepted_at' => now(), 'token_hash' => hash('sha256', Str::random(64))]);

            return $user;
        });

        // An invitation can be completed in the same browser that an Admin or
        // Manager used to create it. The guest-only Login route redirects any
        // authenticated session to /dashboard, where non-customers receive 403.
        // End that prior session so this browser reaches the normal Login page.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Your staff account is ready. Please sign in with your email and new password.');
    }

    private function findInvitation(string $token): ?StaffInvitation
    {
        return StaffInvitation::with('staff')->where('token_hash', hash('sha256', $token))->first();
    }

    private function problem(?StaffInvitation $invitation): ?string
    {
        if (! $invitation) return 'This registration invitation is invalid or has already been used. Contact Admin for a new invitation.';
        if ($invitation->accepted_at) return 'This registration invitation has already been accepted. Contact Admin if you need help.';
        if ($invitation->expires_at->isPast()) return 'This registration invitation has expired. Please contact Admin for a new invitation.';
        if (! $invitation->staff || strtolower(trim($invitation->staff->role)) !== 'staff') return 'This staff record is no longer eligible for registration. Contact Admin.';
        if (User::where('email', $invitation->staff->email)->exists()) return 'An account already exists for this staff member. Contact Admin.';

        return null;
    }
}
