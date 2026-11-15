<?php

namespace App\Providers;

use App\Services\PasswordOtpService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);
    }
}
