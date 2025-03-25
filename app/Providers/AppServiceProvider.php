<?php

namespace App\Providers;

use App\Auth\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\URL;

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
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
