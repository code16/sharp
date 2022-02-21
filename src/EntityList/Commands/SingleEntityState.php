<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class SingleEntityState extends EntityState
{
    public function getGlobalAuthorization(): bool|array
    {
        return $this->authorize();
    }

    final protected function updateState(mixed $instanceId, string $stateId): array
    {
        return $this->updateSingleState($stateId);
    }

    abstract protected function updateSingleState(string $stateId): array;
}
