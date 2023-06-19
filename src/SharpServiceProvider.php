<?php

namespace Code16\Sharp;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Auth\TwoFactor\Engines\GoogleTotpEngine;
use Code16\Sharp\Auth\TwoFactor\Engines\Sharp2faTotpEngine;
use Code16\Sharp\Auth\TwoFactor\Sharp2faEloquentDefaultTotpHandler;
use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler;
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
use Code16\Sharp\Http\Middleware\Api\AppendInstanceAuthorizations;
use Code16\Sharp\Http\Middleware\Api\AppendListAuthorizations;
use Code16\Sharp\Http\Middleware\Api\AppendMultiformInEntityList;
use Code16\Sharp\Http\Middleware\Api\AppendNotifications;
use Code16\Sharp\Http\Middleware\SharpAuthenticate;
use Code16\Sharp\Http\Middleware\SharpRedirectIfAuthenticated;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Code16\Sharp\View\Components\Content;
use Code16\Sharp\View\Components\File;
use Code16\Sharp\View\Components\Image;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProviderLaravelRecent;

class SharpServiceProvider extends ServiceProvider
{
    const VERSION = '7.29.3';

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sharp');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/back', 'sharp');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/front', 'sharp-front');

        $this->publishes([
            __DIR__.'/../resources/assets/dist' => public_path('vendor/sharp'),
        ], 'assets');

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('sharp.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views/components/file.blade.php' => resource_path('views/vendor/sharp/components/file.blade.php'),
            __DIR__.'/../resources/views/components/image.blade.php' => resource_path('views/vendor/sharp/components/image.blade.php'),
            __DIR__.'/../resources/views/partials/plugin-script.blade.php' => resource_path('views/vendor/sharp/partials/plugin-script.blade.php'),
        ], 'views');

        Blade::componentNamespace('Code16\\Sharp\\View\\Components', 'sharp');
        Blade::componentNamespace('Code16\\Sharp\\View\\Components\\Content', 'sharp-content');
        Blade::component(Content::class, 'sharp-content');
        Blade::component(File::class, 'sharp-file');
        Blade::component(Image::class, 'sharp-image');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sharp');

        $this->registerMiddleware();

        $this->app->singleton(
            SharpAuthorizationManager::class,
            SharpAuthorizationManager::class,
        );

        $this->app->singleton(
            CurrentSharpRequest::class,
            CurrentSharpRequest::class,
        );

        $this->app->singleton(
            SharpMenuManager::class,
            SharpMenuManager::class
        );

        if (class_exists("\PragmaRX\Google2FA\Google2FA")) {
            $this->app->bind(
                Sharp2faTotpEngine::class,
                GoogleTotpEngine::class,
            );
        }

        $this->app->bind(
            Sharp2faHandler::class,
            fn () => match(config('sharp.auth.2fa.handler')) {
                'notification' => app(Sharp2faNotificationHandler::class),
                'totp' => app(Sharp2faEloquentDefaultTotpHandler::class),
                default => is_string(config('sharp.auth.2fa.handler'))
                    ? app(config('sharp.auth.2fa.handler'))
                    : value(config('sharp.auth.2fa.handler')),
            }
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

    protected function registerMiddleware(): void
    {
        $this->app['router']
            ->middlewareGroup('sharp_common', $this->app['config']->get('sharp.middleware.common'))
            ->middlewareGroup('sharp_web', $this->app['config']->get('sharp.middleware.web'))
            ->middlewareGroup('sharp_api', $this->app['config']->get('sharp.middleware.api'))
            ->aliasMiddleware('sharp_api_append_instance_authorizations', AppendInstanceAuthorizations::class)
            ->aliasMiddleware('sharp_api_append_list_authorizations', AppendListAuthorizations::class)
            ->aliasMiddleware('sharp_api_append_multiform_in_list', AppendMultiformInEntityList::class)
            ->aliasMiddleware('sharp_api_append_notifications', AppendNotifications::class)
            ->aliasMiddleware('sharp_api_append_breadcrumb', AppendBreadcrumb::class)
            ->aliasMiddleware('sharp_auth', SharpAuthenticate::class)
            ->aliasMiddleware('sharp_guest', SharpRedirectIfAuthenticated::class);
    }
}
