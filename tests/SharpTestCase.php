<?php

namespace Code16\Sharp\Tests;

use Code16\Sharp\SharpServiceProvider;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Orchestra\Testbench\TestCase;

abstract class SharpTestCase extends TestCase
{
    use ArraySubsetAsserts;

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [SharpServiceProvider::class];
    }
}