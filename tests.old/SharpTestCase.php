<?php

namespace Code16\Sharp\Tests;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\SharpServiceProvider;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Contracts\Auth\Access\Gate;
use Orchestra\Testbench\TestCase;

abstract class SharpTestCase extends TestCase
{
    use ArraySubsetAsserts;

    protected function getPackageProviders($app)
    {
        return [SharpServiceProvider::class];
    }

    protected function disableSharpAuthorizationChecks(): void
    {
        $this->app->bind(SharpAuthorizationManager::class, function () {
            return new class(app(SharpEntityManager::class), app(Gate::class)) extends SharpAuthorizationManager
            {
                public function isAllowed(string $ability, string $entityKey, ?string $instanceId = null): bool
                {
                    return true;
                }

                public function check(string $ability, string $entityKey, ?string $instanceId = null): void
                {
                }
            };
        });
    }
}
