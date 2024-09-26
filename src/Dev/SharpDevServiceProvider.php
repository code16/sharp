<?php

namespace Code16\Sharp\Dev;

use Illuminate\Support\ServiceProvider;

class SharpDevServiceProvider extends ServiceProvider
{
    public function register()
    {
        config()->set('ziggy', require __DIR__.'/config/ziggy.php');
        config()->set('typescript-transformer', require __DIR__.'/config/typescript-transformer.php');
    }
}
