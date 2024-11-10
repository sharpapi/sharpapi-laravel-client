<?php

declare(strict_types=1);

namespace SharpAPI\SharpApiService;

use Illuminate\Support\ServiceProvider;

class SharpApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sharpapi-client.php' => config_path('sharpapi-client.php'),
            ], 'sharpapi-laravel-client');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Merge the package configuration with the app configuration.
        $this->mergeConfigFrom(
            __DIR__.'/../config/sharpapi-client.php', 'sharpapi-client'
        );

        // Register the main class to use with the facade
        $this->app->singleton('sharpapi-laravel-client', function () {
            return new SharpApiService();
        });
    }
}
