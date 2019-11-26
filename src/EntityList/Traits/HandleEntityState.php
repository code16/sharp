<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\EntityState;

trait HandleEntityState
{
    /**
     * @var string
     */
    protected $entityStateAttribute;

    /**
     * @var EntityState
     */
    protected $entityStateHandler;

    /**
     * @param string $stateAttribute
     * @param EntityState|string $stateHandlerOrClassName
     * @return $this
     */
    protected function setEntityState(string $stateAttribute, $stateHandlerOrClassName)
    {
        $this->entityStateAttribute = $stateAttribute;

        $this->entityStateHandler = $stateHandlerOrClassName instanceof EntityState
            ? $stateHandlerOrClassName
            : app($stateHandlerOrClassName);

        return $this;
    }

    /**
     * @return EntityState
     */
    public function entityStateHandler()
    {
        return $this->entityStateHandler;
    }

    /**
     * @param array $config
     * @param null $instanceId
     */
    protected function appendEntityStateToConfig(array &$config, $instanceId = null)
    {
        if($this->entityStateAttribute) {
            $config["state"] = [
                "attribute" => $this->entityStateAttribute,
                "values" => collect($this->entityStateHandler->states())
                    ->map(function($state, $key) {
                        return [
                            "value" => $key,
                            "label" => $state[0],
                            "color" => $state[1]
                        ];
                    })->values()->all(),
                "authorization" => $instanceId
                    ? $this->entityStateHandler->authorizeFor($instanceId)
                    : $this->entityStateHandler->getGlobalAuthorization()
            ];
        }
    }
}