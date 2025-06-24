<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(RateLimiter $limiter): void
    {
        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(now()->addHour(1));
        Passport::refreshTokensExpireIn(now()->addHour(1));
        Passport::personalAccessTokensExpireIn(now()->addHour(1));

        $limiter->for('api_kahoot', function ($request) {
            return Limit::perMinute(config('kahoot.rate_limits'))
                ->by(optional(
                    $request->user())->id ?: $request->ip()
                );
        });
    }
}
