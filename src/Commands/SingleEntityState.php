<?php

namespace Code16\Sharp\Commands;

use Code16\Sharp\Commands\Returns\CommandReloadReturn;

abstract class SingleEntityState extends EntityState
{
    public function getGlobalAuthorization(): bool|array
    {
        return $this->authorize();
    }

    final protected function updateState(mixed $instanceId, string $stateId): CommandReloadReturn
    {
        return $this->updateSingleState($stateId);
    }

    abstract protected function updateSingleState(string $stateId): CommandReloadReturn;
}
