<?php

namespace Code16\Sharp\EntitiesList;

use Code16\Sharp\Exceptions\EntitiesList\InvalidEntityStateException;

abstract class EntitiesListState
{
    /**
     * @var array
     */
    protected $states = [];

    const PRIMARY_COLOR = "sharp_primary";
    const SECONDARY_COLOR = "sharp_secondary";
    const GRAY_COLOR = "sharp_gray";
    const LIGHTGRAY_COLOR = "sharp_lightgray";
    const DARKGRAY_COLOR = "sharp_darkgray";

    /**
     * @return array
     */
    public function states()
    {
        $this->buildStates();

        return $this->states;
    }

    public function update($instanceId, $stateId)
    {
        $this->buildStates();

        if(!in_array($stateId, array_keys($this->states))) {
            throw new InvalidEntityStateException($stateId);
        }

        return $this->updateState($instanceId, $stateId);
    }

    /**
     * @param string $key
     * @param string $label
     * @param string|null $color
     */
    protected function addState(string $key, string $label, string $color = null)
    {
        $this->states[$key] = [$label, $color];
    }

    abstract protected function buildStates();
    abstract protected function updateState($instanceId, $stateId);
}