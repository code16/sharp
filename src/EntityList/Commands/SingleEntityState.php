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
     * @param string $instanceId
     * @param string $stateId
     * @return mixed
     */
    final protected function updateState($instanceId, $stateId)
    {
        return $this->updateSingleState($stateId);
    }

    /**
     * @param string $stateId
     * @return mixed
     */
    abstract protected function updateSingleState(string $stateId);
}