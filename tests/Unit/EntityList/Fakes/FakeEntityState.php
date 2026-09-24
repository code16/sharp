<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Fakes;

use Code16\Sharp\Commands\EntityState;
use Code16\Sharp\Commands\Returns\CommandRefreshReturn;
use Code16\Sharp\Commands\Returns\CommandReloadReturn;

class FakeEntityState extends EntityState
{
    protected function buildStates(): void {}

    protected function updateState($instanceId, string $stateId): CommandReloadReturn|CommandRefreshReturn|null
    {
        return null;
    }
}
