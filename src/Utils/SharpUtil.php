<?php

namespace Code16\Sharp\Utils;

use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\Http\Context\SharpContext;
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
}