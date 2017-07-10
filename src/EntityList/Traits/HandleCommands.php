<?php

namespace Code16\Sharp\EntityList\Traits;

trait HandleCommands
{
    /**
     * @var array
     */
    protected $commandHandlers;

    /**
     * @param string $commandName
     * @param string|EntityCommand $commandHandler
     * @return $this
     */
    protected function addEntityCommand(string $commandName, $commandHandler)
    {
        $this->commandHandlers[$commandName] = $commandHandler instanceof EntityCommand
            ? $commandHandler
            : app($commandHandler);

        return $this;
    }
}