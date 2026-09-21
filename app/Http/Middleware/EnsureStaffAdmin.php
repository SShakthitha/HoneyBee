<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffAdmin
{
    /**
     * Allow access only to authenticated staff members with an administrative
     * role. Customer accounts are not staff records.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $isStaff = Staff::where('email', $user->email)->where('role', 'admin')->exists();

        abort_unless($isStaff, 403);

        return $next($request);
    }
}
