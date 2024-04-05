<?php

namespace Code16\Sharp;

use Code16\Sharp\Auth\Impersonate\SharpDefaultEloquentImpersonationHandler;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Auth\TwoFactor\Engines\GoogleTotpEngine;
use Code16\Sharp\Auth\TwoFactor\Engines\Sharp2faTotpEngine;
use Code16\Sharp\Auth\TwoFactor\Sharp2faEloquentDefaultTotpHandler;
use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler;
use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\Config\SharpLegacyConfigBuilder;
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
use Code16\Sharp\Http\Middleware\SharpAuthenticate;
use Code16\Sharp\Http\Middleware\SharpRedirectIfAuthenticated;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;
use Code16\Sharp\View\Components\Content;
use Code16\Sharp\View\Components\File;
use Code16\Sharp\View\Components\Image;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Intervention\Image\ImageManager;

class SharpInternalServiceProvider extends ServiceProvider
{
    const VERSION = '8.3.7';

    public function boot()
    {
        $this->declareMiddleware();
        $this->declareConsoleCommands();
        $this->loadRoutes();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sharp');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'sharp');

        $this->publishes([__DIR__.'/../dist' => public_path('vendor/sharp')], 'assets');
//        $this->publishes([__DIR__.'/../config/config.php' => config_path('sharp.php')], 'config');
        $this->publishes(
            [
                __DIR__.'/../resources/views/components/file.blade.php' => resource_path('views/vendor/sharp/components/file.blade.php'),
                __DIR__.'/../resources/views/components/image.blade.php' => resource_path('views/vendor/sharp/components/image.blade.php'),
                __DIR__.'/../resources/views/partials/plugin-script.blade.php' => resource_path('views/vendor/sharp/partials/plugin-script.blade.php'),
            ],
            'views'
        );

        Blade::componentNamespace('Code16\\Sharp\\View\\Components', 'sharp');
        Blade::componentNamespace('Code16\\Sharp\\View\\Components\\Content', 'sharp-content');
        Blade::component(Content::class, 'sharp-content');
        Blade::component(File::class, 'sharp-file');
        Blade::component(Image::class, 'sharp-image');

        if (config('sharp.locale')) {
            setlocale(LC_ALL, config('sharp.locale'));
            Carbon::setLocale(config('sharp.locale'));
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sharp');

        $this->app->singleton(SharpAuthorizationManager::class);
        $this->app->singleton(CurrentSharpRequest::class);
        $this->app->singleton(SharpMenuManager::class);
        $this->app->singleton(SharpUploadManager::class);
        $this->app->singleton(
            SharpConfigBuilder::class,
            fn () => file_exists(config_path('sharp.php'))
                ? new SharpLegacyConfigBuilder()
                : new SharpConfigBuilder()
        );
        $this->app->singleton(
            ImageManager::class,
            fn () => new ImageManager(sharpConfig()->get('uploads.image_driver'))
        );

        if (class_exists('\PragmaRX\Google2FA\Google2FA')) {
            $this->app->bind(
                Sharp2faTotpEngine::class,
                GoogleTotpEngine::class,
            );
        }

        $this->app->bind(
            Sharp2faHandler::class,
            fn () => match (config('sharp.auth.2fa.handler')) {
                'notification' => app(Sharp2faNotificationHandler::class),
                'totp' => app(Sharp2faEloquentDefaultTotpHandler::class),
                default => is_string(config('sharp.auth.2fa.handler'))
                    ? app(config('sharp.auth.2fa.handler'))
                    : value(config('sharp.auth.2fa.handler')),
            }
        );

        $this->app->bind(SharpImpersonationHandler::class, function () {
            return sharpConfig()->get('auth.impersonate.enabled')
                ? sharpConfig()->get('auth.impersonate.handler')
                : null;
        });

        $this->app->register(\Intervention\Image\Laravel\ServiceProvider::class);
        $this->app->register(InertiaServiceProvider::class);
    }

    protected function declareMiddleware(): void
    {
        $this->app['router']
            ->middlewareGroup('sharp_common', sharpConfig()->get('middleware.common'))
            ->middlewareGroup('sharp_web', sharpConfig()->get('middleware.web'))
            ->middlewareGroup('sharp_api', sharpConfig()->get('middleware.api'))
            ->aliasMiddleware('sharp_auth', SharpAuthenticate::class)
            ->aliasMiddleware('sharp_guest', SharpRedirectIfAuthenticated::class);
    }

    private function declareConsoleCommands():void
    {
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
    }

    public function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/auth/login.php');

        if (sharpConfig()->get('auth.forgotten_password.enabled')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/auth/forgotten_password.php');

            ResetPassword::createUrlUsing(function ($user, string $token) {
                return route('code16.sharp.password.reset', [
                    'token' => $token,
                    'email' => ($user->email ?? null),
                ]);
            });
        }

        if (sharpConfig()->get('auth.impersonate.enabled')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/auth/impersonate.php');
        }
    }
}