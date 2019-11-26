<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class SingleEntityState extends EntityState
{

    /**
     * @param string $instanceId
     * @param string $stateId
     * @return mixed
     */
    protected function updateState($instanceId, $stateId)
    {
        return $this->updateSingleState($stateId);
    }

    /**
     * @param string $stateId
     * @return mixed
     */
    abstract protected function updateSingleState(string $stateId);
}