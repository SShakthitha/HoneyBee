<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Mail\PasswordOtp as PasswordOtpMail;

class PasswordOtpService
{
    public function __construct(
        private int $expiryMinutes,
        private int $maxAttempts,
        private int $digits,
        private string $purpose,
        private array $rateLimit,
    ) {
    }

    /**
     * Request an OTP for the given email address.
     *
     * Returns true when an OTP was queued for delivery. Returns false (without
     * revealing anything) when no matching user exists, so the endpoint never
     * discloses whether an email address is registered.
     */
    public function requestOtp(string $email): bool
    {
        $email = strtolower(trim($email));

        $user = User::where('email', $email)->first();
        if (! $user) {
            // Generic no-op so unknown and known emails behave identically.
            return false;
        }

        $ip = request()->ip();
        $key = 'password_otp.request.'.sha1($email.'|'.$ip);

        if (RateLimiter::tooManyAttempts($key, $this->rateLimit['request']['max_attempts'])) {
            return false;
        }

        RateLimiter::hit($key, $this->rateLimit['request']['decay_seconds']);

        // Invalidate any prior active OTPs for this user/purpose so only one
        // outstanding code exists at a time.
        $this->invalidateOutstanding($user);

        $otp = $this->generateOtp();
        $expiresAt = now()->addMinutes($this->expiryMinutes);

        DB::table('password_otp_tokens')->insert([
            'user_id' => $user->id,
            'email' => $email,
            'purpose' => $this->purpose,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($email)->send(new PasswordOtpMail($user, $otp, $this->expiryMinutes));

        return true;
    }

    /**
     * Verify an OTP and return the associated user if valid.
     *
     * Uses a transaction with row locking to avoid race conditions around
     * consuming the code. Increments attempts on each failure and rejects
     * once max_attempts is exceeded.
     *
     * @return User|null
     */
    public function verifyOtp(string $email, string $otp): ?User
    {
        $email = strtolower(trim($email));

        $ip = request()->ip();
        $verifyKey = 'password_otp.verify.'.sha1($email.'|'.$ip);

        if (RateLimiter::tooManyAttempts($verifyKey, $this->rateLimit['verify']['max_attempts'])) {
            return null;
        }

        return DB::transaction(function () use ($email, $otp, $verifyKey) {
            $record = DB::table('password_otp_tokens')
                ->where('email', $email)
                ->where('purpose', $this->purpose)
                ->whereNull('consumed_at')
                ->where('expires_at', '>', now())
                ->orderByDesc('created_at')
                ->lockForUpdate()
                ->first();

            if (! $record) {
                RateLimiter::hit($verifyKey, $this->rateLimit['verify']['decay_seconds']);
                return null;
            }

            $attempts = (int) $record->attempts + 1;

            if ($attempts > $this->maxAttempts) {
                DB::table('password_otp_tokens')
                    ->where('id', $record->id)
                    ->update(['consumed_at' => now()]);

                RateLimiter::hit($verifyKey, $this->rateLimit['verify']['decay_seconds']);
                return null;
            }

            $valid = Hash::check($otp, $record->otp_hash);

            DB::table('password_otp_tokens')
                ->where('id', $record->id)
                ->update(['attempts' => $attempts]);

            if (! $valid) {
                RateLimiter::hit($verifyKey, $this->rateLimit['verify']['decay_seconds']);
                return null;
            }

            // OTP is single-use: mark it consumed immediately.
            DB::table('password_otp_tokens')
                ->where('id', $record->id)
                ->update(['consumed_at' => now()]);

            RateLimiter::clear($verifyKey);

            return User::find($record->user_id);
        });
    }

    /**
     * Invalidate all outstanding (unconsumed, unexpired) OTPs for a user.
     */
    public function invalidateOutstanding(User $user): void
    {
        DB::table('password_otp_tokens')
            ->where('user_id', $user->id)
            ->where('purpose', $this->purpose)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->update(['consumed_at' => now()]);
    }

    private function generateOtp(): string
    {
        $min = str_repeat('1', $this->digits); // e.g. 100000 for 6 digits
        $max = str_repeat('9', $this->digits); // e.g. 999999 for 6 digits

        return (string) random_int((int) $min, (int) $max);
    }
}
