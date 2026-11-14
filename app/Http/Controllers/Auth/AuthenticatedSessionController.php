<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $email = $request->user()->email;
        $staff = Staff::where('email', $email)->first();
        $staffRole = strtolower((string) $staff?->role);

        if ($staffRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($staffRole === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        if ($staffRole === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        if (! $staff && Customer::where('email', $email)->exists()) {
            return redirect()->route('customer.dashboard');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'This account does not have a dashboard role assigned. Contact HoneyBee support.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
