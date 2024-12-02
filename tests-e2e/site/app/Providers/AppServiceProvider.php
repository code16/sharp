<?php

namespace App\Providers;

use Code16\Sharp\SharpInternalServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(SharpInternalServiceProvider::class);
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
