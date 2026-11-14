# HoneyBee pre-deployment checklist

This document prepares a future production handoff. It does not change the local WAMP environment or deploy the application.

## Application environment

- Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` to the final HTTPS origin in the hosting environment only.
- Generate a unique `APP_KEY` for that environment. Store it and all credentials in the host's secret manager or protected environment settings; never commit an `.env` file.
- Configure a dedicated production database account with only the required database privileges. Require encrypted database connections when supported by the host.
- Set `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`, and configure the session domain only when the final domain requires it.
- Configure trusted proxy settings only for the actual hosting proxy or load balancer.

## Files, database, and operations

- Confirm `storage` and `bootstrap/cache` are writable by the application process and are not writable by unrelated system users.
- Make `public/storage` available using Laravel's storage link; keep private files outside the public disk.
- Take and test a restorable database backup before every production migration. Review pending migrations against a staging copy first.
- Configure queue workers and scheduled tasks only for features that use them. Keep workers supervised and restart them during releases.
- Send application errors to a protected log destination and configure uptime/error monitoring without recording passwords, session cookies, tokens, or payment data.
- Define database backup retention and perform a restore rehearsal before accepting customer orders.

## Release verification

- Run `composer install --no-dev --prefer-dist --optimize-autoloader` and `npm ci` in the release pipeline, then `npm run build`.
- Run `composer audit --locked --no-dev`, `npm audit --omit=dev`, PHP syntax checks, route checks, Pint, and the full PHPUnit suite against isolated test data.
- Apply production migrations only after the backup and staging checks pass; use Laravel's production migration confirmation.
- Cache configuration, routes, and views only after the production environment values are installed and validated.
- Verify HTTPS redirects, secure cookies, login and role boundaries, checkout and order confirmation, contact/WhatsApp links, and error handling in staging.
- Keep HoneyBee in development until the host, database, secrets, TLS, backups, monitoring, and staging smoke tests have been supplied and verified.
