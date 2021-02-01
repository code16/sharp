<?php

namespace Code16\Sharp;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Form\Eloquent\Uploads\Migration\CreateUploadsMigration;
use Code16\Sharp\Http\Composers\AssetViewComposer;
use Code16\Sharp\Http\Composers\MenuViewComposer;
use Code16\Sharp\Http\Middleware\Api\AddSharpContext;
use Code16\Sharp\Http\Middleware\Api\AppendBreadcrumb;
use Code16\Sharp\Http\Middleware\Api\AppendFormAuthorizations;
use Code16\Sharp\Http\Middleware\Api\AppendListAuthorizations;
use Code16\Sharp\Http\Middleware\Api\AppendListMultiform;
use Code16\Sharp\Http\Middleware\Api\AppendNotifications;
use Code16\Sharp\Http\Middleware\Api\BindSharpValidationResolver;
use Code16\Sharp\Http\Middleware\Api\HandleSharpApiErrors;
use Code16\Sharp\Http\Middleware\Api\SetSharpLocale;
use Code16\Sharp\Http\Middleware\InvalidateCache;
use Code16\Sharp\Http\Middleware\SharpAuthenticate;
use Code16\Sharp\Http\Middleware\SharpRedirectIfAuthenticated;
use Code16\Sharp\Http\Middleware\StoreBreadcrumb;
use Code16\Sharp\Http\SharpContext;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProviderLaravelRecent;

class SharpServiceProvider extends ServiceProvider
{
    /** @var string */
    const VERSION = '5.4.3';

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sharp');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/back', 'sharp');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/front', 'sharp-front');

        $this->publishes([
            __DIR__.'/../resources/assets/dist' => public_path('vendor/sharp')
        ], 'assets');

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('sharp.php'),
        ], 'config');

        $this->registerPolicies();

        view()->composer(
            ['sharp::form', 'sharp::show', 'sharp::list', 'sharp::dashboard', 'sharp::welcome'],
            MenuViewComposer::class
        );

        view()->composer(
            ['sharp::form','sharp::show', 'sharp::list', 'sharp::dashboard', 'sharp::welcome', 'sharp::login', 'sharp::unauthorized'],
            AssetViewComposer::class
        );
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sharp');

        $this->registerMiddleware();

        $this->app->singleton(
            SharpContext::class, SharpContext::class
        );

        $this->app->singleton(
            SharpAuthorizationManager::class, SharpAuthorizationManager::class
        );

        $this->commands([
            CreateUploadsMigration::class,
            \Code16\Sharp\Console\ListMakeCommand::class,
            \Code16\Sharp\Console\FormMakeCommand::class,
            \Code16\Sharp\Console\SingleFormMakeCommand::class,
            \Code16\Sharp\Console\ShowMakeCommand::class,
            \Code16\Sharp\Console\SingleShowMakeCommand::class,
            \Code16\Sharp\Console\StateMakeCommand::class,
            \Code16\Sharp\Console\MediaMakeCommand::class,
            \Code16\Sharp\Console\PolicyMakeCommand::class,
            \Code16\Sharp\Console\ModelWizardCommand::class,
            \Code16\Sharp\Console\EntityCommandMakeCommand::class,
            \Code16\Sharp\Console\InstanceCommandMakeCommand::class,
            \Code16\Sharp\Console\DashboardMakeCommand::class,
            \Code16\Sharp\Console\ValidatorMakeCommand::class,
            \Code16\Sharp\Console\ListFilterMakeCommand::class,
            \Code16\Sharp\Console\ReorderHandlerMakeCommand::class,
        ]);

        $this->app->register(ImageServiceProviderLaravelRecent::class);
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

        foreach((array)config("sharp.dashboards") as $dashboardKey => $config) {
            if(isset($config["policy"])) {
                $this->definePolicy($dashboardKey, $config["policy"], 'view');
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

    protected function registerMiddleware()
    {
        $this->app['router']->middlewareGroup("sharp_web", [
            \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $this->app['router']->aliasMiddleware(
            'sharp_api_append_form_authorizations', AppendFormAuthorizations::class

        )->aliasMiddleware(
            'sharp_api_append_list_authorizations', AppendListAuthorizations::class

        )->aliasMiddleware(
            'sharp_api_append_list_multiform', AppendListMultiform::class

        )->aliasMiddleware(
            'sharp_api_append_notifications', AppendNotifications::class

        )->aliasMiddleware(
            'sharp_api_errors', HandleSharpApiErrors::class

        )->aliasMiddleware(
            'sharp_api_context', AddSharpContext::class

        )->aliasMiddleware(
            'sharp_api_validation', BindSharpValidationResolver::class

        )->aliasMiddleware(
            'sharp_api_append_breadcrumb', AppendBreadcrumb::class

        )->aliasMiddleware(
            'sharp_locale', SetSharpLocale::class

        )->aliasMiddleware(
            'sharp_auth', SharpAuthenticate::class

        )->aliasMiddleware(
            'sharp_guest', SharpRedirectIfAuthenticated::class

        )->aliasMiddleware(
            'sharp_store_breadcrumb', StoreBreadcrumb::class

        )->aliasMiddleware(
            'sharp_invalidate_cache', InvalidateCache::class
        );
    }
}
