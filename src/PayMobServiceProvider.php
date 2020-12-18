<?php

namespace ctf0\PayMob;

use Illuminate\Support\ServiceProvider;

class PayMobServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'paymob');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'paymob');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/paymob.php', 'paymob');

        // Register the service the package provides.
        $this->app->singleton('paymob', function ($app) {
            return new PayMob();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['paymob'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/config/paymob.php' => config_path('paymob.php'),
        ], 'config');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/resources/views' => base_path('resources/views/vendor/paymob'),
        ], 'views');

        // Publishing the translation files.
        $this->publishes([
            __DIR__ . '/resources/lang' => resource_path('lang/vendor/paymob'),
        ], 'translation');
    }
}
