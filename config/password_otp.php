<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password OTP Configuration
    |--------------------------------------------------------------------------
    |
    | These options control the email-based one-time password (OTP) flow that
    | replaces Laravel's default password-reset-link experience.
    |
    */

    /* Number of minutes an OTP remains valid after generation. */
    'expiry' => (int) env('PASSWORD_OTP_EXPIRY_MINUTES', 10),

    /* Maximum number of verification attempts allowed per OTP before it is rejected. */
    'max_attempts' => (int) env('PASSWORD_OTP_MAX_ATTEMPTS', 5),

    /* Length of the generated numeric OTP (must be at least 6 digits). */
    'digits' => (int) env('PASSWORD_OTP_DIGITS', 6),

    /* Purpose label stored alongside each OTP record. */
    'purpose' => env('PASSWORD_OTP_PURPOSE', 'password-reset'),

    /*
    |--------------------------------------------------------------------------
    | Rate limiting
    |--------------------------------------------------------------------------
    |
    | Limits are applied per (email, IP) pair to prevent rapid resend abuse
    | and brute-force verification attempts.
    |
    | max_attempts: number of attempts allowed within the decay window (seconds).
    | decay_seconds: the window length in seconds.
    */
    'rate_limit' => [
        'request' => [
            'max_attempts' => (int) env('PASSWORD_OTP_RATE_LIMIT_REQUEST_MAX', 3),
            'decay_seconds' => (int) env('PASSWORD_OTP_RATE_LIMIT_REQUEST_DECAY', 300),
        ],
        'verify' => [
            'max_attempts' => (int) env('PASSWORD_OTP_RATE_LIMIT_VERIFY_MAX', 10),
            'decay_seconds' => (int) env('PASSWORD_OTP_RATE_LIMIT_VERIFY_DECAY', 300),
        ],
    ],

];
