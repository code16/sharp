<?php

namespace App\Providers;

use Code16\Sharp\Config\SharpConfigBuilder;
use Illuminate\Support\ServiceProvider;

class DemoSharpServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        app(SharpConfigBuilder::class)
            ->setName('Demo project')
            ->setCustomUrlSegment('demo-sharp');
    }
}