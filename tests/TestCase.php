<?php

namespace Code16\Sharp\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use BladeUI\Icons\Factory;
use Code16\ContentRenderer\ContentRendererServiceProvider;
use Code16\Sharp\SharpInternalServiceProvider;
use Orchestra\Testbench\Pest\WithPest;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelImageOptimizer\ImageOptimizerServiceProvider;
use Spatie\LaravelPasskeys\LaravelPasskeysServiceProvider;

class TestCase extends Orchestra
{
    use WithPest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SharpInternalServiceProvider::class,
            ContentRendererServiceProvider::class,
            BladeIconsServiceProvider::class,
            ImageOptimizerServiceProvider::class,
            LaravelPasskeysServiceProvider::class,
        ];
    }

    public function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('view.cache', false);
        $app['config']->set('inertia.pages.paths', [__DIR__.'/../resources/js/Pages']);
        $app['config']->set('inertia.testing.ensure_pages_exist', true);
        $app['config']->set('database.default', 'testing');

        $app['view']->addNamespace('fixtures', __DIR__.'/Fixtures/resources/views');

        $app->make(Factory::class)->add('testicon', [
            'path' => __DIR__.'/Fixtures/resources/svg',
            'prefix' => 'testicon',
        ]);
    }
}
