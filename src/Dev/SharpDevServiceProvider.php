<?php

namespace Code16\Sharp\Dev;

use Code16\Sharp\Dev\Commands\UpdateIdeJsonCommand;
use Illuminate\Support\ServiceProvider;

class SharpDevServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            config()->set('ziggy', require __DIR__.'/config/ziggy.php');
            config()->set('typescript-transformer', require __DIR__.'/config/typescript-transformer.php');

            $this->commands([
                UpdateIdeJsonCommand::class,
            ]);
        }
    }
}
