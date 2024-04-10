<?php

namespace Code16\Sharp;

use Code16\Sharp\Config\SharpConfigBuilder;
use Illuminate\Support\ServiceProvider;

abstract class SharpAppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->accessGate();
    }

    public function register()
    {
        $this->configureSharp(app(SharpConfigBuilder::class));
    }

    public function accessGate(): void
    {
    }

    abstract protected function configureSharp(SharpConfigBuilder $config): void;
}
