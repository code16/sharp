<?php

namespace Code16\Sharp;

use Code16\Sharp\Http\Middleware\AddSharpContext;
use Code16\Sharp\Http\Middleware\HandleSharpErrors;
use Code16\Sharp\Http\SharpContext;
use Illuminate\Support\ServiceProvider;

class SharpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sharp');

        $this->publishes([
            __DIR__.'/../resources/assets/dist' => public_path('vendor/sharp')
        ], 'assets');
    }

    public function register()
    {
        $this->app['router']->aliasMiddleware(
            'sharp_errors', HandleSharpErrors::class
        );

        $this->app['router']->aliasMiddleware(
            'sharp_context', AddSharpContext::class
        );

        $this->app->singleton(
            SharpContext::class, SharpContext::class
        );
    }
}