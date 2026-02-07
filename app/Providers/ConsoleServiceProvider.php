<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only load commands when running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ImportInventoryData::class,
            ]);
        }
    }
}