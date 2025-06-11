<?php

namespace Code16\Sharp\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Code16\ContentRenderer\ContentRendererServiceProvider;
use Code16\Sharp\SharpInternalServiceProvider;
use Illuminate\Testing\Fluent\AssertableJson;
use Orchestra\Testbench\TestCase as Orchestra;
use PHPUnit\Framework\Assert as PHPUnit;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        config()->set('view.cache', false);
        config()->set('inertia.testing.page_paths', [__DIR__.'/../resources/js/Pages']);

        // laravel 11 polyfill, TODO to remove when laravel 12+ only
        AssertableJson::macro('whereNull', function ($key) {
            $this->has($key);

            $actual = $this->prop($key);

            PHPUnit::assertNull(
                $actual,
                sprintf(
                    'Property [%s] should be null.',
                    $this->dotPath($key),
                )
            );

            return $this;
        });
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
