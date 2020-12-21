<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\EntityState;

trait HandleEntityState
{
    protected ?string $entityStateAttribute = null;
    protected ?EntityState $entityStateHandler = null;

    protected function setEntityState(string $stateAttribute, $stateHandlerOrClassName): self
    {
        $this->entityStateAttribute = $stateAttribute;

        $this->entityStateHandler = $stateHandlerOrClassName instanceof EntityState
            ? $stateHandlerOrClassName
            : app($stateHandlerOrClassName);

        return $this;
    }

    public function entityStateHandler(): EntityState
    {
        return $this->entityStateHandler;
    }

    protected function appendEntityStateToConfig(array &$config, $instanceId = null): void
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