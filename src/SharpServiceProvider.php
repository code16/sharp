<?php

namespace Code16\Sharp;

use Code16\Sharp\Form\Eloquent\Uploads\Migration\CreateUploadsMigration;
use Code16\Sharp\Http\Middleware\AddSharpContext;
use Code16\Sharp\Http\Middleware\CheckSharpAuthorizations;
use Code16\Sharp\Http\Middleware\HandleSharpErrors;
use Code16\Sharp\Http\SharpContext;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProviderLaravel5;

class SharpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sharp');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/back', 'sharp');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/front', 'sharp-front');

        $this->publishes([
            __DIR__.'/../resources/assets/dist' => public_path('vendor/sharp')
        ], 'assets');
    }

    public function register()
    {
        $this->app['router']->aliasMiddleware(
            'sharp_authorizations', CheckSharpAuthorizations::class

        )->aliasMiddleware(
            'sharp_errors', HandleSharpErrors::class

        )->aliasMiddleware(
            'sharp_context', AddSharpContext::class
        );

        $this->app->singleton(
            SharpContext::class, SharpContext::class
        );

        $this->commands([
            CreateUploadsMigration::class
        ]);

        $this->app->register(ImageServiceProviderLaravel5::class);

        $this->registerPolicies();
    }

    private function registerPolicies()
    {
        foreach((array)config("sharp.entities") as $entityKey => $config) {
            if(isset($config["policy"])) {
                Gate::policy("sharp.{$entityKey}", $config["policy"]);
            }
        }
    }
}