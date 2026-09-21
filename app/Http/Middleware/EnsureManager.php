<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManager
{
    /** Restrict staff administration in the operational workspace to managers. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        abort_unless(Staff::where('email', $user->email)->where('role', 'manager')->exists(), 403);

        return $next($request);
    }
}
