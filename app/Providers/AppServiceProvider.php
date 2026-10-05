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
        if (!app()->runningInConsole()) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('orders') || !\Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_status')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
                }
            } catch (\Throwable $e) {
                // Ignore if DB connection is unavailable
            }
        }
    }
}
