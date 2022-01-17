<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\EntityList\Commands\SingleEntityState;
use Code16\Sharp\Exceptions\SharpException;

abstract class SharpSingleShow extends SharpShow
{
    final public function showConfig(mixed $instanceId, array $config = []): array
    {
        return parent::showConfig(null, [
            'isSingle' => true,
        ]);
    }

    public function find($id): array
    {
        return $this->findSingle();
    }

    protected function setEntityState(string $stateAttribute, $stateHandlerOrClassName): self
    {
        $entityStateHandler = is_string($stateHandlerOrClassName)
            ? app($stateHandlerOrClassName)
            : $stateHandlerOrClassName;

        if (!$entityStateHandler instanceof SingleEntityState) {
            throw new SharpException(
                sprintf(
                    "Handler class for entity state handler [%s] is not an subclass of %s as it should be since it's a part of a SharpSingleShow",
                    $stateAttribute,
                    SingleEntityState::class
                )
            );
        }

        return parent::configureEntityState($stateAttribute, $stateHandlerOrClassName);
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     */
    abstract public function findSingle(): array;
}
