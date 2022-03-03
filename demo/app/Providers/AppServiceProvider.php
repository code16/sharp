<?php

namespace App\Providers;

use App\Media;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\SharpServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(SharpServiceProvider::class);
        $this->app->bind(SharpUploadModel::class, Media::class);
    }

    public function boot()
    {
        //
    }
}
