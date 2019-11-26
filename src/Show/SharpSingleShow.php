<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Commands\SingleEntityState;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Exceptions\SharpException;

abstract class SharpSingleShow extends SharpShow
{
    /*
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    function find($id): array
    {
        return $this->findSingle();
    }

    /**
     * @param string $commandName
     * @param string|InstanceCommand $commandHandlerOrClassName
     * @return SharpShow
     * @throws SharpException
     */
    protected function addInstanceCommand(string $commandName, $commandHandlerOrClassName)
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        if(!$commandHandler instanceof SingleInstanceCommand) {
            throw new SharpException(
                sprintf(
                    "Handler class for instance command [%s] is not an subclass of %s as it should be since it's a part of a SharpSingleShow",
                    $commandName,
                    SingleInstanceCommand::class
                )
            );
        }

        return parent::addInstanceCommand($commandName, $commandHandlerOrClassName);
    }

    /**
     * @param string $stateAttribute
     * @param EntityState|string $stateHandlerOrClassName
     * @return SharpShow
     * @throws SharpException
     */
    protected function setEntityState(string $stateAttribute, $stateHandlerOrClassName)
    {
        $entityStateHandler = is_string($stateHandlerOrClassName)
            ? app($stateHandlerOrClassName)
            : $stateHandlerOrClassName;

        if(!$entityStateHandler instanceof SingleEntityState) {
            throw new SharpException(
                sprintf(
                    "Handler class for entity state handler [%s] is not an subclass of %s as it should be since it's a part of a SharpSingleShow",
                    $stateAttribute,
                    SingleEntityState::class
                )
            );
        }

        return parent::setEntityState($stateAttribute, $stateHandlerOrClassName);
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @return array
     */
    abstract function findSingle(): array;
}