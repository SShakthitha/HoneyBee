<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    /** Display the password reset view. */
    public function create(): View|RedirectResponse
    {
        $email = session('reset_email', old('email'));

        if (! $email) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', [
            'email' => $email,
        ]);
    }

    /** Verify the email OTP before showing the new-password form. */
    public function verify(Request $request, PasswordOtpService $otp): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'otp' => ['required', 'string', 'digits:'.config('password_otp.digits')],
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();
        $verifiedUser = $user ? $otp->verifyOtp($email, $request->otp) : null;

        if (! $verifiedUser) {
            return back()->withInput(['email' => $email])
                ->withErrors(['otp' => __('The reset code is invalid, expired, or has been used too many times.')]);
        }

        $request->session()->regenerate();
        $request->session()->put([
            'password_reset_verified_user_id' => $verifiedUser->id,
            'password_reset_verified_at' => now()->timestamp,
        ]);

        return redirect()->route('password.new');
    }

    /** Show the password form only after this session verified a valid OTP. */
    public function createPassword(Request $request): View|RedirectResponse
    {
        if (! $this->hasRecentOtpVerification($request)) {
            $this->clearVerificationSession($request);

            return redirect()->route('password.request')
                ->withErrors(['email' => __('Please request and verify a reset code first.')]);
        }

        return view('auth.new-password');
    }

    /**
     * Handle an incoming new password request using a verified OTP.
     *
     * @throws ValidationException
     */
    public function store(Request $request, PasswordOtpService $otp): RedirectResponse
    {
        if (! $this->hasRecentOtpVerification($request)) {
            $this->clearVerificationSession($request);

            return redirect()->route('password.request')
                ->withErrors(['email' => __('Please request and verify a reset code first.')]);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::find($request->session()->get('password_reset_verified_user_id'));
        if (! $user) {
            $this->clearVerificationSession($request);

            return redirect()->route('password.request')
                ->withErrors(['email' => __('Please request and verify a reset code first.')]);
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
        $email = strtolower($user->email);
        RateLimiter::clear('password_otp.request.'.sha1($email.'|'.$request->ip()));
        RateLimiter::clear('password_otp.verify.'.sha1($email.'|'.$request->ip()));
        $this->clearVerificationSession($request);

        return redirect()->route('login')->with('status', __('Your password has been reset. Please log in with your new password.'));
    }

    private function hasRecentOtpVerification(Request $request): bool
    {
        $verifiedAt = (int) $request->session()->get('password_reset_verified_at', 0);
        $userId = $request->session()->get('password_reset_verified_user_id');
        $validForSeconds = (int) config('password_otp.expiry', 10) * 60;

        return $userId !== null
            && $verifiedAt > 0
            && $verifiedAt <= now()->timestamp
            && now()->timestamp - $verifiedAt <= $validForSeconds;
    }

    private function clearVerificationSession(Request $request): void
    {
        $request->session()->forget([
            'password_reset_verified_user_id',
            'password_reset_verified_at',
        ]);
    }
}
