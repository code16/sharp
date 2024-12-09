<?php

namespace Code16\Sharp;

use Code16\Sharp\Config\SharpConfigBuilder;
use Illuminate\Support\ServiceProvider;

abstract class SharpAppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->declareAccessGate();
    }

    public function register()
    {
        $this->configureSharp(app(SharpConfigBuilder::class));
    }

    protected function declareAccessGate(): void {}

    abstract protected function configureSharp(SharpConfigBuilder $config): void;
}
