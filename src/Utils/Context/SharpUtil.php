<?php

namespace Code16\Sharp\Utils\Context;

use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\SharpInternalServiceProvider;

class SharpUtil
{
    protected SharpContext $context;

    public function __construct()
    {
        $this->context = new SharpContext();
    }

    public function version(): string
    {
        return SharpInternalServiceProvider::VERSION;
    }

    public function config(): SharpConfigBuilder
    {
        return app(SharpConfigBuilder::class);
    }

    public function context(): SharpContext
    {
        return $this->context;
    }

    public function request()
    {
        
    }
}