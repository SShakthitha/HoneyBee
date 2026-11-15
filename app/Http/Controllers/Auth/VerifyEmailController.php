<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RoleDashboardRouter;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request, RoleDashboardRouter $dashboardRouter): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($dashboardRouter->routeNameFor($request->user()) ?? 'login');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $route = $dashboardRouter->routeNameFor($request->user()) ?? 'login';

        return redirect()->route($route, $route === 'login' ? [] : ['verified' => 1]);
    }
}
