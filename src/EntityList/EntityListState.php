<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\Exceptions\EntityList\InvalidEntityStateException;

abstract class EntityListState
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

        return $this->updateState($instanceId, $stateId) ?: $this->refresh();
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

    /**
     * Send back a reload action to the client.
     * TODO: generalize to Commands
     *
     * @return array
     */
    protected function reload()
    {
        return ["action" => "reload"];
    }

    /**
     * Send back a refresh action to the client.
     * TODO: generalize to Commands
     *
     * @param array|null $entityIds
     * @return array
     */
    protected function refresh(array $entityIds = null)
    {
        // TODO find a way to load data for entities with $entityIds
        return array_merge(
            ["action" => "refresh"],
            $entityIds ? ["items" => $entityIds] : []
        );
    }

    /**
     * @return mixed
     */
    abstract protected function buildStates();

    /**
     * @param string $instanceId
     * @param string $stateId
     * @return mixed
     */
    abstract protected function updateState($instanceId, $stateId);
}