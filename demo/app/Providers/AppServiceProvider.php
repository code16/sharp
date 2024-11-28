<?php

namespace App\Providers;

use Code16\Sharp\Dev\SharpDevServiceProvider;
use Code16\Sharp\SharpInternalServiceProvider;
use Code16\Sharp\View\Components\ViteWrapper as SharpViteWrapperComponent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //        $this->app->bind(SharpUploadModel::class, Media::class)
        
        $this->app->register(SharpInternalServiceProvider::class);
        $this->app->register(DemoSharpServiceProvider::class);
        
        if (class_exists(SharpDevServiceProvider::class)) {
            $this->app->register(SharpDevServiceProvider::class);
        }

        $this->app->bind(SharpViteWrapperComponent::class, function () {
            return new SharpViteWrapperComponent(hotFile: base_path('../dist/hot'));
        });
    }

    public function boot(): void
    {
    }
}
