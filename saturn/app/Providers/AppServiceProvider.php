<?php

namespace App\Providers;

use Code16\Sharp\SharpServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        // Include Sharp's routes
//        include __DIR__.'/../../../src/routes.php';
//
//        // Include Sharp's views
//        $this->loadViewsFrom(__DIR__.'/../../../resources/views', 'sharp');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(SharpServiceProvider::class);
    }
}
