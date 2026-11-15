<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffInvitationAdminController extends Controller
{
    public function issue(Request $request, int $id): RedirectResponse
    {
        $staff = Staff::where('staff_id', $id)->firstOrFail();
        abort_unless(strtolower(trim($staff->role)) === 'staff', 422, 'Invitations can only be issued to staff-role records.');
        abort_if(User::where('email', $staff->email)->exists(), 409, 'This staff member already has a login account.');

        $token = Str::random(64);
        StaffInvitation::updateOrCreate(['staff_id' => $staff->staff_id], [
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDay(),
            'accepted_at' => null,
        ]);
        // Use the origin and base path the administrator actually used to reach
        // the app. APP_URL often omits artisan's port or a WAMP subdirectory.
        $url = $request->getSchemeAndHttpHost()
            .$request->getBaseUrl()
            .route('staff.invitation.register', ['token' => $token], false);

        return redirect()->route('admin.staff')->with('invitation', [
            'name' => $staff->full_name,
            'email' => $staff->email,
            'url' => $url,
            'expires_at' => now()->addDay()->toDayDateTimeString(),
        ])->with('success', 'Registration invitation generated. Copy and share the link with the staff member.');
    }
}
