<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /** Display the password reset request view. */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset OTP request.
     *
     * Sends the OTP email (if the account exists) and redirects to the
     * reset-password page with the email pre-filled. The generic flash
     * message never reveals whether the address is registered.
     */
    public function store(Request $request, PasswordOtpService $otp): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $sent = $otp->requestOtp($request->email);

        if ($sent === false) {
            // Could mean no account OR a mail failure. Use a generic message
            // that does not reveal account existence, but also covers mail errors.
            return back()->with('status', __(
                'If an account exists for that email, a 6-digit reset code has been sent. Please check your inbox.'
            ));
        }

        return redirect()->route('password.reset')
            ->with('reset_email', strtolower(trim($request->email)))
            ->with('status', __(
                'If an account exists for that email, a 6-digit reset code has been sent. Please check your inbox.'
            ));
    }
}
