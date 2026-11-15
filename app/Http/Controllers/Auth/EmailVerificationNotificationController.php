<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RoleDashboardRouter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

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

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors([
                'email' => __("We couldn't send the verification email right now. Please try again later."),
            ]);
        }

        return back()->with('status', 'verification-link-sent');
    }
}
