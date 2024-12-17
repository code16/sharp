<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Fakes;

use Code16\Sharp\EntityList\Commands\EntityState;

class FakeEntityState extends EntityState
{
    protected function buildStates(): void {}

    protected function updateState($instanceId, string $stateId): ?array
    {
        return null;
    }
}
