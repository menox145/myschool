<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

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

    public function boot()
    {
        if (app()->environment('local')) {
            $host = request()->getHost();
            if (str_contains($host, 'trycloudflare.com')) {
                URL::forceRootUrl('https://' . $host);
                URL::forceScheme('https');
                Config::set('session.domain', '.trycloudflare.com');
                Config::set('session.secure', false);
            }
        }
    }
}
