<?php

namespace App\Providers;

use Code16\Sharp\SharpInternalServiceProvider;
use Code16\Sharp\View\Components\ViteWrapper as SharpViteWrapperComponent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(SharpInternalServiceProvider::class);
        $this->app->bind(SharpViteWrapperComponent::class, function () {
            return new SharpViteWrapperComponent(hotFile: base_path('../../dist/hot'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->register(SharpServiceProvider::class);
        //
    }
}
