<?php

namespace Code16\Sharp\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Code16\ContentRenderer\ContentRendererServiceProvider;
use Code16\Sharp\SharpInternalServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        config()->set('view.cache', false);
    }

    protected function getPackageProviders($app)
    {
        return [
            SharpInternalServiceProvider::class,
            ContentRendererServiceProvider::class,
            BladeIconsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $app->make(\BladeUI\Icons\Factory::class)->add('testicon', [
            'path' => __DIR__.'/Fixtures/resources/svg',
            'prefix' => 'testicon',
        ]);

        $app->make('view')->addNamespace('fixtures', __DIR__.'/Fixtures/resources/views');

        // We have to set these two because otherwise corresponding routes won't be loaded at all
        sharp()->config()
            ->enableForgottenPassword()
            ->enableImpersonation();
    }
}
