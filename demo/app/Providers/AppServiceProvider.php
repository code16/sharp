<?php

namespace App\Providers;

use Code16\Sharp\SharpServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(SharpServiceProvider::class);
    }

    public function boot()
    {
        //
    }
}
