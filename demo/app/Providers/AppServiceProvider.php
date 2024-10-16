<?php

namespace App\Providers;

use Code16\Sharp\Dev\SharpDevServiceProvider;
use Code16\Sharp\SharpInternalServiceProvider;
use Code16\Sharp\View\Components\Vite as SharpViteComponent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
//        $this->app->bind(SharpUploadModel::class, Media::class)

        $this->app->bind(SharpViteComponent::class, function () {
            return new SharpViteComponent(hotFile: base_path('../dist/hot'));
        });
    }

    public function boot(): void
    {
        $this->app->register(SharpInternalServiceProvider::class);
        $this->app->register(DemoSharpServiceProvider::class);

        if (class_exists(SharpDevServiceProvider::class)) {
            $this->app->register(SharpDevServiceProvider::class);
        }
    }
}
