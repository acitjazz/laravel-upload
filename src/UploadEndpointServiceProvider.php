<?php

namespace Acitjazz\UploadEndpoint;

use Illuminate\Support\ServiceProvider;

class UploadEndpointServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'acitjazz');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'acitjazz');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

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
        
        $this->mergeConfigFrom(__DIR__.'/../config/uploadendpoint.php', 'uploadendpoint');

        // Register the service the package provides.
        $this->app->singleton('uploadendpoint', function ($app) {
            return new UploadEndpoint;
        });
    }

    /**
     * Register Sanctum's migration files.
     *
     * @return void
     */
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['uploadendpoint'];
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
            __DIR__.'/../config/uploadendpoint.php' => config_path('uploadendpoint.php'),
        ], 'acitjazz-upload-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'acitjazz-media-migrations');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/acitjazz'),
        ], 'uploadendpoint.views');*/

        // Publishing component.
        $this->publishes([
            __DIR__.'/../resources/js/Components' => resource_path('js/Components'),
        ], 'acitjazz-vue-component');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/acitjazz'),
        ], 'uploadendpoint.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
