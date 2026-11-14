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

        $staffRole = Staff::where('email', $user->email)->value('role');

        abort_unless(strtolower((string) $staffRole) === 'admin', 403);

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
