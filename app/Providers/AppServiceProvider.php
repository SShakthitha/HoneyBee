<?php

namespace App\Providers;

use App\Services\PasswordOtpService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PasswordOtpService::class, function ($app) {
            $config = config('password_otp');

            return new PasswordOtpService(
                expiryMinutes: (int) $config['expiry'],
                maxAttempts: (int) $config['max_attempts'],
                digits: (int) $config['digits'],
                purpose: (string) $config['purpose'],
                rateLimit: $config['rate_limit'],
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
