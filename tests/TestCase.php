<?php

namespace Code16\Sharp\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use BladeUI\Icons\Factory;
use Code16\ContentRenderer\ContentRendererServiceProvider;
use Code16\Sharp\SharpInternalServiceProvider;
use Illuminate\Support\Arr;
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
        $app['config']->set('session.serialization', 'json');

        $app['view']->addNamespace('fixtures', __DIR__.'/Fixtures/resources/views');

        $app->make(Factory::class)->add('testicon', [
            'path' => __DIR__.'/Fixtures/resources/svg',
            'prefix' => 'testicon',
        ]);
    }

    /**
     * With `session.serialization` set to `json`, only scalars and arrays survive from one
     * request to the next. The test session store keeps its values in memory between test
     * requests, so we check before and after each request that the session would survive.
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $this->assertSessionSurvivesJsonSerialization();

        $response = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);

        $this->assertSessionSurvivesJsonSerialization();

        return $response;
    }

    protected function assertSessionSurvivesJsonSerialization(): void
    {
        if ($this->app['config']->get('session.serialization') !== 'json') {
            return;
        }

        // Laravel's session store converts the ViewErrorBag to and from arrays itself
        foreach (Arr::except($this->app['session.store']->all(), 'errors') as $key => $value) {
            $this->assertSame(
                $value,
                json_decode((string) json_encode($value), true),
                sprintf('Session key [%s] holds a value that does not survive JSON session serialization.', $key),
            );
        }
    }
}
