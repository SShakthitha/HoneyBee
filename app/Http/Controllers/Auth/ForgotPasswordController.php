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

        $otp->requestOtp($request->email);

        // Redirect to the reset page and pre-fill the email field via session.
        // The status message is intentionally generic to avoid disclosing
        // whether an account exists for the given address.
        return redirect()->route('password.reset')
            ->with('reset_email', strtolower(trim($request->email)))
            ->with('status', __(
                'If an account exists for that email, a 6-digit reset code has been sent. Please check your inbox.'
            ));
    }
}
