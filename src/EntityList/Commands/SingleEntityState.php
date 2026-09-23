<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\Commands\Returns\CommandRefreshReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandReloadReturn;

abstract class SingleEntityState extends EntityState
{
    public function getGlobalAuthorization(): bool|array
    {
        return $this->authorize();
    }

    final protected function updateState(mixed $instanceId, string $stateId): CommandReloadReturn|CommandRefreshReturn|null
    {
        return $this->updateSingleState($stateId);
    }

    abstract protected function updateSingleState(string $stateId): ?CommandReloadReturn;
}
