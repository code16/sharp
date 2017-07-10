<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\Command;

trait HandleCommands
{
    /**
     * @var array
     */
    protected $commandHandlers = [];

    /**
     * @param string $commandName
     * @param string|Command $commandHandler
     * @return $this
     */
    protected function addEntityCommand(string $commandName, $commandHandler)
    {
        $this->commandHandlers[$commandName] = $commandHandler instanceof Command
            ? $commandHandler
            : app($commandHandler);

        return $this;
    }

    protected function appendCommandsToConfig(array &$config)
    {
        foreach($this->commandHandlers as $commandName => $handler) {
            $config["commands"][] = [
                "key" => $commandName,
                "label" => $handler->label(),
                "type" => $handler->type()
            ];
        }
    }
}