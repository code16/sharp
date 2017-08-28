<?php

namespace Code16\Sharp\Tests;

use Code16\Sharp\SharpServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class SharpTestCase extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [SharpServiceProvider::class];
    }
}