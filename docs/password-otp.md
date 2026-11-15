# HoneyBee password reset — email OTP flow

This document describes the email-based one-time password (OTP) flow that
replaces Laravel's default password-reset-link experience in HoneyBee.

## Architecture

HoneyBee remains a single deployable Laravel application (Blade, Vite,
Tailwind CSS, Alpine.js). No separate frontend app, microservice, or SMS
provider is introduced.

Password reset is now OTP-based:

1. The user submits their email on `/forgot-password`.
2. If an account exists, the server generates a 6-digit cryptographically
   secure OTP, stores only its bcrypt hash in `password_otp_tokens`, and
   emails the plaintext OTP to the user.
3. The user enters the OTP and a new password (with confirmation) on
   `/reset-password`.
4. On a valid, unexpired, single-use OTP, the password is hashed and updated,
   all outstanding OTPs are invalidated, and the user is redirected to login.

## Account model constraint

Authentication uses the `users` table only (`App\Models\User`, the `web`
guard). The `customers` and `staff` tables are email-linked profile tables
with no credentials and cannot log in independently. Therefore the OTP
password-reset flow is available only to accounts that have a `users` record.
Registration already creates both a `User` and a linked `Customer` record in
one transaction, so newly registered customers are covered. Staff accounts
that exist only in the `staff` table (without a `User` record) cannot reset a
password through this flow — this is intentional and preserves current
authentication behavior.

## Routes

All routes live in `routes/auth.php` inside the `guest` middleware group.
Route names are unchanged so existing links keep working:

| Name | URI | Controller |
|------|-----|------------|
| `password.request` | `GET /forgot-password` | `ForgotPasswordController@create` |
| `password.email` | `POST /forgot-password` | `ForgotPasswordController@store` |
| `password.reset` | `GET /reset-password` | `ResetPasswordController@create` |
| `password.store` | `POST /reset-password` | `ResetPasswordController@store` |

The old token-based URL (`reset-password/{token}`) is removed; the OTP is
submitted in the form body instead, so there is no accidental reset-link
bypass.

## Security properties

- **Generic responses:** The forgot-password endpoint returns the same
  success message whether or not the email is registered, preventing account
  enumeration.
- **Hash-only storage:** Only `Hash::make($otp)` is stored; the plaintext OTP
  exists only in memory during the request and is never logged in production.
- **Expiry:** OTPs expire after `PASSWORD_OTP_EXPIRY_MINUTES` (default 10).
- **Attempts:** A maximum of `PASSWORD_OTP_MAX_ATTEMPTS` (default 5)
  verification attempts is enforced per OTP; exceeding it consumes the code.
- **Single use:** An OTP is marked consumed immediately after a successful
  reset and all outstanding OTPs for the user are invalidated.
- **Rate limiting:** OTP requests and verification attempts are throttled per
  (email, IP) pair via Laravel's `RateLimiter`.
- **Race safety:** OTP consumption runs inside a database transaction with
  row locking (`lockForUpdate`).
- **CSRF:** All forms use `@csrf`.
- **Password hashing:** New passwords use `Hash::make()` with Laravel's
  `Password::defaults()` validation rules.

## Configuration

Add these to `.env` (placeholders in `.env.example`; never commit real
credentials):

```env
# Mail delivery — required in production. Use smtp, ses, or postmark.
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="HoneyBee"

# OTP settings (secure defaults shown)
PASSWORD_OTP_EXPIRY_MINUTES=10
PASSWORD_OTP_MAX_ATTEMPTS=5
PASSWORD_OTP_DIGITS=6
PASSWORD_OTP_PURPOSE=password-reset
PASSWORD_OTP_RATE_LIMIT_REQUEST_MAX=3
PASSWORD_OTP_RATE_LIMIT_REQUEST_DECAY=300
PASSWORD_OTP_RATE_LIMIT_VERIFY_MAX=10
PASSWORD_OTP_RATE_LIMIT_VERIFY_DECAY=300
```

`config/password_otp.php` reads these values with secure defaults.

## Local development

- `MAIL_MAILER=log` (default in `.env.example`) writes emails to `storage/logs`
  so you can inspect the OTP without a real mail provider.
- Run migrations: `php artisan migrate` creates `password_otp_tokens`.
- Run tests: `vendor/bin/phpunit tests/Feature/Auth`.

## Production requirements

- PHP 8.2+ with Composer dependencies installed (`composer install --no-dev --optimize-autoloader`).
- Node/Vite asset build (`npm ci && npm run build`).
- Database migrations applied (`php artisan migrate --force`).
- Writable `storage` and `bootstrap/cache` directories.
- `APP_KEY` generated and `APP_URL` set to the HTTPS origin.
- `APP_ENV=production`, `APP_DEBUG=false`.
- A real mail provider configured (SMTP, SES, or Postmark); do not rely on
  the `log` mailer in production.
- HTTPS/TLS terminated at the load balancer or app server.

## Tests

`tests/Feature/Auth/PasswordOtpTest.php` covers valid OTP, invalid OTP, expiry,
too many attempts, resend throttling, unknown-email generic response,
successful password change, OTP single-use behavior, and password validation.
