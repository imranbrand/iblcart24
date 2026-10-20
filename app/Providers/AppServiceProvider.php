<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Load shop settings saved from the admin panel (ignored until the
        // settings table has been migrated).
        try {
            \App\Models\Setting::applyToConfig();
        } catch (\Throwable $e) {
            //
        }
    }
}
