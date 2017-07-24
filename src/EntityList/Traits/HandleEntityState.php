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
     * @param EntityState|string $stateHandler
     * @return $this
     */
    protected function setEntityState(string $stateAttribute, $stateHandler)
    {
        $this->entityStateAttribute = $stateAttribute;

        $this->entityStateHandler = $stateHandler instanceof EntityState
            ? $stateHandler
            : app($stateHandler);

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
     */
    protected function appendEntityStateToConfig(array &$config)
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
                "authorization" => $this->entityStateHandler->getGlobalAuthorization()
            ];
        }
    }
}