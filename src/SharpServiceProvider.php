<?php

namespace Code16\Sharp;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Auth\SharpGate;
use Code16\Sharp\Form\Eloquent\Uploads\Migration\CreateUploadsMigration;
use Code16\Sharp\Http\Composers\MenuViewComposer;
use Code16\Sharp\Http\Middleware\Api\AddSharpContext;
use Code16\Sharp\Http\Middleware\Api\AppendFormAuthorizations;
use Code16\Sharp\Http\Middleware\Api\AppendListAuthorizations;
use Code16\Sharp\Http\Middleware\Api\HandleSharpApiErrors;
use Code16\Sharp\Http\Middleware\CheckIsSharpAuthenticated;
use Code16\Sharp\Http\Middleware\CheckIsSharpGuest;
use Code16\Sharp\Http\SharpContext;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProviderLaravel5;

class SharpServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    const VERSION = '4.0.0b';

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sharp');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/back', 'sharp');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/front', 'sharp-front');

        $this->publishes([
            __DIR__.'/../resources/assets/dist' => public_path('vendor/sharp')
        ], 'assets');

        $this->registerPolicies();

        view()->composer(
            ['sharp::form', 'sharp::list'],
            MenuViewComposer::class
        );
    }

    public function register()
    {
        $this->app['router']->aliasMiddleware(
            'sharp_api_append_form_authorizations', AppendFormAuthorizations::class

        )->aliasMiddleware(
            'sharp_api_append_list_authorizations', AppendListAuthorizations::class

        )->aliasMiddleware(
            'sharp_api_errors', HandleSharpApiErrors::class

        )->aliasMiddleware(
            'sharp_api_context', AddSharpContext::class

        )->aliasMiddleware(
            'sharp_auth', CheckIsSharpAuthenticated::class

        )->aliasMiddleware(
            'sharp_guest', CheckIsSharpGuest::class
        );

        $this->app->singleton(
            SharpContext::class, SharpContext::class
        );

        $this->app->singleton(
            SharpAuthorizationManager::class, SharpAuthorizationManager::class
        );

        // Override Laravel's Gate to handle Sharp's ability to define a custom Guard
        $this->app->singleton(GateContract::class, function ($app) {
            return new SharpGate($app);
        });

        $this->commands([
            CreateUploadsMigration::class
        ]);

        $this->app->register(ImageServiceProviderLaravel5::class);
    }

    protected function registerPolicies()
    {
        foreach((array)config("sharp.entities") as $entityKey => $config) {
            if(isset($config["policy"])) {
                foreach(['entity', 'view', 'update', 'create', 'delete'] as $action) {
                    $this->definePolicy($entityKey, $config["policy"], $action);
                }
            }
        }
    }

    /**
     * @param string $entityKey
     * @param string $policy
     * @param string $action
     */
    protected function definePolicy($entityKey, $policy, $action)
    {
        if(method_exists(app($policy), $action)) {
            Gate::define("sharp.{$entityKey}.{$action}", $policy . "@{$action}");

        } else {
            // No policy = true by default
            Gate::define("sharp.{$entityKey}.{$action}", function () {
                return true;
            });
        }
    }
}