<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Exceptions\EntityList\InvalidEntityStateException;

abstract class EntityState extends Command
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

        return $this->updateState($instanceId, $stateId) ?: $this->refresh($instanceId);
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
     * @return string
     */
    public function type(): string
    {
        return "state";
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return "";
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