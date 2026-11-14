<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    /** Allow customer-only pages to accounts with a customer record and no staff role. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $isStaff = Staff::where('email', $user->email)->exists();
        $isCustomer = Customer::where('email', $user->email)->exists();

        abort_unless($isCustomer && ! $isStaff, 403);

        return $next($request);
    }
}
