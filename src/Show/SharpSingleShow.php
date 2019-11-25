<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
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
            throw new SharpException("Handler class for instance command [{$commandName}] is not an subclass of " . SingleInstanceCommand::class);
        }

        return parent::addInstanceCommand($commandName, $commandHandlerOrClassName);
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @return array
     */
    abstract function findSingle(): array;
}