<?php

namespace Code16\Sharp\Tests\Pest;

use Code16\Sharp\SharpServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            SharpServiceProvider::class,
        ];
    }

//    public function getEnvironmentSetUp($app)
//    {
//        config()->set('database.default', 'testing');
//    }
}
