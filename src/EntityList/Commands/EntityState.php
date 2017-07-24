<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\Exceptions\EntityList\InvalidEntityStateException;
use Exception;

/**
 * Base class for applicative Entity States.
 *
 * Class EntityState
 * @package Code16\Sharp\EntityList\Commands
 */
abstract class EntityState extends InstanceCommand
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

    /**
     * @param string $key
     * @param string $label
     * @param string|null $color
     * @return $this
     */
    protected function addState(string $key, string $label, string $color = null)
    {
        $this->states[$key] = [$label, $color];

        return $this;
    }

    /**
     * @param string $bladeView
     * @param array $params
     * @return array|void
     * @throws Exception
     */
    protected function view(string $bladeView, array $params = [])
    {
        throw new Exception("View return type is not supported for a state.");
    }

    /**
     * @param string $message
     * @return array|void
     * @throws Exception
     */
    protected function info(string $message)
    {
        throw new Exception("Info return type is not supported for a state.");
    }

    /**
     * @param string $instanceId
     * @param array $params
     * @return array
     * @throws InvalidEntityStateException
     */
    public function execute($instanceId, array $params = [])
    {
        $stateId = $params["value"];
        $this->buildStates();

        if(!in_array($stateId, array_keys($this->states))) {
            throw new InvalidEntityStateException($stateId);
        }

        return $this->updateState($instanceId, $stateId) ?: $this->refresh($instanceId);
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