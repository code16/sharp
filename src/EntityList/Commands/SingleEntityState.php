<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class SingleEntityState extends EntityState
{
    /**
     * @return bool|array
     */
    public function getGlobalAuthorization()
    {
        return $this->authorize();
    }

    /**
     * @param mixed $instanceId
     * @param string $stateId
     * @return array
     */
    final protected function updateState($instanceId, string $stateId): array
    {
        return $this->updateSingleState($stateId);
    }

    abstract protected function updateSingleState(string $stateId): array;
}