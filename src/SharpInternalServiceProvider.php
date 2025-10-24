<?php

namespace Code16\Sharp;

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
use Code16\Sharp\Console\EntityMakeCommand;
use Code16\Sharp\Console\EntityStateMakeCommand;
use Code16\Sharp\Console\FormMakeCommand;
use Code16\Sharp\Console\GeneratorCommand;
use Code16\Sharp\Console\InstallCommand;
use Code16\Sharp\Console\InstanceCommandMakeCommand;
use Code16\Sharp\Console\MediaMakeCommand;
use Code16\Sharp\Console\MenuMakeCommand;
use Code16\Sharp\Console\PolicyMakeCommand;
use Code16\Sharp\Console\ReorderHandlerMakeCommand;
use Code16\Sharp\Console\ServiceProviderMakeCommand;
use Code16\Sharp\Console\ShowPageMakeCommand;
use Code16\Sharp\Exceptions\SharpAuthenticationException;
use Code16\Sharp\Exceptions\SharpTokenMismatchException;
use Code16\Sharp\Form\Eloquent\Uploads\Migration\CreateUploadsMigration;
use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\SharpImageManager;
use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Http\Middleware\AddLinkHeadersForPreloadedRequests;
use Code16\Sharp\Http\Middleware\SharpAuthenticate;
use Code16\Sharp\Http\Middleware\SharpRedirectIfAuthenticated;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Code16\Sharp\Utils\SharpUtil;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;
use Code16\Sharp\View\Components\Content;
use Code16\Sharp\View\Components\File;
use Code16\Sharp\View\Components\Image;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TickReceived;

class SharpInternalServiceProvider extends ServiceProvider
{
    const VERSION = '9.11.1';

    public function boot()
    {
        $this->declareMiddleware();
        $this->declareConsoleCommands();
        $this->loadRoutes();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sharp');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'sharp');

        $this->publishes([__DIR__.'/../dist' => public_path('vendor/sharp')], 'sharp-assets');
        $this->publishes(
            [
                __DIR__.'/../resources/views/components/file.blade.php' => resource_path('views/vendor/sharp/components/file.blade.php'),
                __DIR__.'/../resources/views/components/image.blade.php' => resource_path('views/vendor/sharp/components/image.blade.php'),
                __DIR__.'/../resources/views/partials/plugin-script.blade.php' => resource_path('views/vendor/sharp/partials/plugin-script.blade.php'),
            ],
            'sharp-views'
        );

        Blade::componentNamespace('Code16\\Sharp\\View\\Components', 'sharp');
        Blade::componentNamespace('Code16\\Sharp\\View\\Components\\Content', 'sharp-content');
        Blade::component(Content::class, 'sharp-content');
        Blade::component(File::class, 'sharp-file');
        Blade::component(Image::class, 'sharp-image');

        $this->registerViewExceptionMapper();

        if (config('sharp.locale')) {
            setlocale(LC_ALL, config('sharp.locale'));
            Carbon::setLocale(config('sharp.locale'));
        }

        $this->configureOctane();
    }

    public function register()
    {
        $this->app->singleton(SharpAuthorizationManager::class);
        $this->app->singleton(CurrentSharpRequest::class);
        $this->app->singleton(SharpMenuManager::class);
        $this->app->singleton(SharpUploadManager::class);
        $this->app->singleton(SharpUtil::class);
        $this->app->singleton(
            SharpConfigBuilder::class,
            fn () => file_exists(config_path('sharp.php'))
                ? new SharpLegacyConfigBuilder()
                : new SharpConfigBuilder()
        );
        $this->app->singleton(SharpImageManager::class);
        $this->app->singleton(AddLinkHeadersForPreloadedRequests::class);

        if (class_exists('\PragmaRX\Google2FA\Google2FA')) {
            $this->app->bind(
                Sharp2faTotpEngine::class,
                GoogleTotpEngine::class,
            );
        }

        $this->app->bind(
            Sharp2faHandler::class,
            fn () => match (sharp()->config()->get('auth.2fa.handler')) {
                'notification' => app(Sharp2faNotificationHandler::class),
                'totp' => app(Sharp2faEloquentDefaultTotpHandler::class),
                default => sharp()->config()->get('auth.2fa.handler'),
            }
        );

        $this->app->bind(SharpImpersonationHandler::class, function () {
            return sharp()->config()->get('auth.impersonate.enabled')
                ? sharp()->config()->get('auth.impersonate.handler')
                : null;
        });

        $this->app->register(InertiaServiceProvider::class);
    }

    protected function declareMiddleware(): void
    {
        $this->app->make('router')
            ->middlewareGroup('sharp_common', sharp()->config()->get('middleware.common'))
            ->middlewareGroup('sharp_web', sharp()->config()->get('middleware.web'))
            ->middlewareGroup('sharp_api', sharp()->config()->get('middleware.api'))
            ->aliasMiddleware('sharp_auth', SharpAuthenticate::class)
            ->aliasMiddleware('sharp_guest', SharpRedirectIfAuthenticated::class);
    }

    private function declareConsoleCommands(): void
    {
        $this->commands([
            CreateUploadsMigration::class,
            InstallCommand::class,
            GeneratorCommand::class,
            ServiceProviderMakeCommand::class,
            EntityMakeCommand::class,
            EntityListMakeCommand::class,
            FormMakeCommand::class,
            ShowPageMakeCommand::class,
            EntityStateMakeCommand::class,
            MediaMakeCommand::class,
            EntityCommandMakeCommand::class,
            InstanceCommandMakeCommand::class,
            DashboardMakeCommand::class,
            PolicyMakeCommand::class,
            EntityListFilterMakeCommand::class,
            ReorderHandlerMakeCommand::class,
            MenuMakeCommand::class,
        ]);
    }

    protected function registerViewExceptionMapper(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        if (! method_exists($handler, 'map')) {
            return;
        }

        $handler->map(function (TokenMismatchException $exception) {
            if (request()->routeIs('code16.sharp.*')) {
                return new SharpTokenMismatchException($exception);
            }

            return $exception;
        });

        $handler->map(function (AuthenticationException $exception) {
            if (request()->routeIs('code16.sharp.*')) {
                return new SharpAuthenticationException(
                    $exception->getMessage(),
                    $exception->guards(),
                    $exception->redirectTo(request())
                );
            }

            return $exception;
        });
    }

    public function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/routes/auth/login.php');

        if (sharp()->config()->get('auth.forgotten_password.enabled')) {
            $this->loadRoutesFrom(__DIR__.'/routes/auth/forgotten_password.php');

            ResetPassword::createUrlUsing(function ($user, string $token) {
                return route('code16.sharp.password.reset', [
                    'token' => $token,
                    'email' => ($user->email ?? null),
                ]);
            });
        }

        if (sharp()->config()->get('auth.impersonate.enabled')) {
            $this->loadRoutesFrom(__DIR__.'/routes/auth/impersonate.php');
        }
    }

    private function configureOctane(): void
    {
        if (isset($_SERVER['LARAVEL_OCTANE'])) {
            $this->app['events']->listen(RequestReceived::class, function () {
                $this->resetSharp();
            });

            $this->app['events']->listen(TaskReceived::class, function () {
                $this->resetSharp();
            });

            $this->app['events']->listen(TickReceived::class, function () {
                $this->resetSharp();
            });

            $this->app['events']->listen(RequestTerminated::class, function () {
                $this->resetSharp();
            });
        }
    }

    private function resetSharp()
    {
        $this->app->get(SharpMenuManager::class)->reset();
        $this->app->get(SharpAuthorizationManager::class)->reset();
        $this->app->get(SharpUploadManager::class)->reset();
        $this->app->get(SharpUtil::class)->__construct();
        $this->app->get(SharpImageManager::class)->__construct();
        $this->app->get(AddLinkHeadersForPreloadedRequests::class)->reset();
    }
}
