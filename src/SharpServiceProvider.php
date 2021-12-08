<?php

namespace Code16\Sharp;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Console\DashboardMakeCommand;
use Code16\Sharp\Console\EntityCommandMakeCommand;
use Code16\Sharp\Console\EntityListFilterMakeCommand;
use Code16\Sharp\Console\EntityListMakeCommand;
use Code16\Sharp\Console\FormMakeCommand;
use Code16\Sharp\Console\InstanceCommandMakeCommand;
use Code16\Sharp\Console\MediaMakeCommand;
use Code16\Sharp\Console\ReorderHandlerMakeCommand;
use Code16\Sharp\Console\ShowPageMakeCommand;
use Code16\Sharp\Console\StateMakeCommand;
use Code16\Sharp\Console\ValidatorMakeCommand;
use Code16\Sharp\Form\Eloquent\Uploads\Migration\CreateUploadsMigration;
use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Http\Middleware\Api\AppendBreadcrumb;
use Code16\Sharp\Http\Middleware\Api\AppendFormAuthorizations;
use Code16\Sharp\Http\Middleware\Api\AppendListAuthorizations;
use Code16\Sharp\Http\Middleware\Api\AppendMultiformInEntityList;
use Code16\Sharp\Http\Middleware\Api\AppendNotifications;
use Code16\Sharp\Http\Middleware\Api\BindSharpValidationResolver;
use Code16\Sharp\Http\Middleware\Api\HandleSharpApiErrors;
use Code16\Sharp\Http\Middleware\Api\SetSharpLocale;
use Code16\Sharp\Http\Middleware\InvalidateCache;
use Code16\Sharp\Http\Middleware\SharpAuthenticate;
use Code16\Sharp\Http\Middleware\SharpRedirectIfAuthenticated;
use Code16\Sharp\View\Components\Content;
use Code16\Sharp\View\Components\File;
use Code16\Sharp\View\Components\Image;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProviderLaravelRecent;

class SharpServiceProvider extends ServiceProvider
{
    const VERSION = '7.0.0-beta.3';

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

        $this->publishes([
            __DIR__ . '/../resources/views/components/file.blade.php' => resource_path('views/vendor/sharp/components/file.blade.php'),
            __DIR__ . '/../resources/views/components/image.blade.php' => resource_path('views/vendor/sharp/components/image.blade.php'),
        ], 'views');
        
        Blade::componentNamespace('Code16\\Sharp\\View\\Components', 'sharp');
        Blade::componentNamespace('Code16\\Sharp\\View\\Components\\Content', 'sharp-content');
        Blade::component(Content::class, 'sharp-content');
        Blade::component(File::class, 'sharp-file');
        Blade::component(Image::class, 'sharp-image');
    
        $this->registerPolicies();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sharp');

        $this->registerMiddleware();

        $this->app->singleton(
            SharpAuthorizationManager::class,
            SharpAuthorizationManager::class
        );

        $this->app->singleton(
            CurrentSharpRequest::class,
            CurrentSharpRequest::class
        );

        $this->commands([
            CreateUploadsMigration::class,
            EntityListMakeCommand::class,
            FormMakeCommand::class,
            ShowPageMakeCommand::class,
            StateMakeCommand::class,
            MediaMakeCommand::class,
            EntityCommandMakeCommand::class,
            InstanceCommandMakeCommand::class,
            DashboardMakeCommand::class,
            ValidatorMakeCommand::class,
            EntityListFilterMakeCommand::class,
            ReorderHandlerMakeCommand::class,
        ]);

        $this->app->register(ImageServiceProviderLaravelRecent::class);
    }

    protected function registerPolicies(): void
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

    protected function definePolicy(string $entityKey, string $policy, string $action): void
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

    protected function registerMiddleware(): void
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

        $this->app['router']
            ->aliasMiddleware(
                'sharp_api_append_form_authorizations',
                AppendFormAuthorizations::class
            )
            ->aliasMiddleware(
                'sharp_api_append_list_authorizations',
                AppendListAuthorizations::class
            )
            ->aliasMiddleware(
                'sharp_api_append_multiform_in_list',
                AppendMultiformInEntityList::class
            )
            ->aliasMiddleware(
                'sharp_api_append_notifications',
                AppendNotifications::class
            )
            ->aliasMiddleware(
                'sharp_api_errors',
                HandleSharpApiErrors::class
            )
            ->aliasMiddleware(
                'sharp_api_validation',
                BindSharpValidationResolver::class
            )
            ->aliasMiddleware(
                'sharp_api_append_breadcrumb',
                AppendBreadcrumb::class
            )
            ->aliasMiddleware(
                'sharp_locale',
                SetSharpLocale::class
            )
            ->aliasMiddleware(
                'sharp_auth',
                SharpAuthenticate::class
            )
            ->aliasMiddleware(
                'sharp_guest',
                SharpRedirectIfAuthenticated::class
            )
            ->aliasMiddleware(
                'sharp_invalidate_cache',
                InvalidateCache::class
            );
    }
}
