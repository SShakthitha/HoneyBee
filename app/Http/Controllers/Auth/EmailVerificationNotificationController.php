<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RoleDashboardRouter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request, RoleDashboardRouter $dashboardRouter): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($dashboardRouter->routeNameFor($request->user()) ?? 'login');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
