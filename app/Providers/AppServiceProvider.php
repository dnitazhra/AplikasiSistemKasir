<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
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
        if (! app()->runningInConsole()) {
            try {
                if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'payment_status')) {
                    Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
                }
            } catch (\Throwable $e) {
                // Ignore if DB connection is unavailable
            }
        }
    }
}
