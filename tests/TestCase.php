<?php

namespace Code16\Sharp\Tests;

use Code16\ContentRenderer\ContentRendererServiceProvider;
use Code16\Sharp\SharpServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use InteractsWithViews;
    
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
            ContentRendererServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // We have to set these two because otherwise corresponding routes won't be loaded at all
        config()->set('sharp.auth.forgotten_password.enabled', true);
        config()->set('sharp.auth.impersonate.enabled', true);
    }
}
