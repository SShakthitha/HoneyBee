<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    /** Display the password reset view. */
    public function create(): View
    {
        return view('auth.reset-password', [
            'email' => session('reset_email', old('email')),
        ]);
    }

    /**
     * Handle an incoming new password request using a verified OTP.
     *
     * @throws ValidationException
     */
    public function store(Request $request, PasswordOtpService $otp): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'otp' => ['required', 'string', 'digits:'.config('password_otp.digits')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $email = strtolower(trim($request->email));

        // Ensure a matching account exists before verifying the OTP so the
        // response does not vary based on account existence.
        $user = User::where('email', $email)->first();
        if (! $user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('We could not find an account for that email address.')]);
        }

        $verifiedUser = $otp->verifyOtp($email, $request->otp);
        if (! $verifiedUser) {
            return back()->withInput($request->only('email'))
                ->withErrors(['otp' => __('The reset code is invalid, expired, or has been used too many times.')]);
        }

        DB::transaction(function () use ($user, $request) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        // Invalidate all outstanding OTPs now that the password has changed.
        $otp->invalidateOutstanding($user);

        // Clear any per-email/IP rate-limit state so a legitimate user is not
        // penalized after a successful reset.
        RateLimiter::clear('password_otp.request.'.sha1($email.'|'.request()->ip()));
        RateLimiter::clear('password_otp.verify.'.sha1($email.'|'.request()->ip()));

        return redirect()->route('login')->with('status', __('Your password has been reset. Please log in with your new password.'));
    }
}
