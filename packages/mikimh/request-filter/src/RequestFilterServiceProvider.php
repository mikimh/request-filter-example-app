<?php

namespace Mikimh\RequestFilter;

use Illuminate\Support\ServiceProvider;

class RequestFilterServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mikimh');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'mikimh');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/request-filter.php', 'request-filter');

        // Register the service the package provides.
        $this->app->singleton('request-filter', function ($app) {
            return new Mikimh\RequestFilter\Facades\RequestFilter;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['request-filter'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/request-filter.php' => config_path('request-filter.php'),
        ], 'request-filter.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/mikimh'),
        ], 'request-filter.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/mikimh'),
        ], 'request-filter.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/mikimh'),
        ], 'request-filter.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
