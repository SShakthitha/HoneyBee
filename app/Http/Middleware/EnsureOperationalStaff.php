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

        abort_unless(Staff::where('email', $user->email)->whereIn('role', ['staff', 'manager'])->exists(), 403);

        return $next($request);
    }
}
