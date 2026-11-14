<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOperationalStaff
{
    /** Allow only operational staff into the separate staff workspace. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $staffRole = strtolower((string) Staff::where('email', $user->email)->value('role'));

        abort_unless(in_array($staffRole, ['staff', 'manager'], true), 403);

        return $next($request);
    }
}
