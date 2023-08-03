<?php

namespace App\Providers;

use App\Support\Vite;
use Code16\Sharp\SharpServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(SharpServiceProvider::class);
//        $this->app->bind(SharpUploadModel::class, Media::class)
        $this->app->singleton(\Illuminate\Foundation\Vite::class, Vite::class);
    }

    public function boot()
    {
        //
    }
}
