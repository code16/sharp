<?php

namespace Code16\Sharp\Tests;

use Code16\Sharp\SharpServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
//    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutVite();

        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    protected function getPackageProviders($app)
    {
        return [
            SharpServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
//        config()->set('database.default', 'testing');
    }
}
