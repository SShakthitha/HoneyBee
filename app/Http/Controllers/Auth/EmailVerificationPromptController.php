<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RoleDashboardRouter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request, RoleDashboardRouter $dashboardRouter): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->route($dashboardRouter->routeNameFor($request->user()) ?? 'login')
                    : view('auth.verify-email');
    }
}
