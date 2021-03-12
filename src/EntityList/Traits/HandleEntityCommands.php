<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Exceptions\SharpException;

trait HandleEntityCommands
{
    protected array $entityCommandHandlers = [];
    protected int $entityCommandCurrentGroupNumber = 0;

    protected function addEntityCommand(string $commandName, $commandHandlerOrClassName): self
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        if(!$commandHandler instanceof EntityCommand) {
            throw new SharpException("Handler class for entity command [{$commandName}] is not an subclass of " . EntityCommand::class);
        }

        $commandHandler->setGroupIndex($this->entityCommandCurrentGroupNumber);

        $this->entityCommandHandlers[$commandName] = $commandHandler;

        return $this;
    }

    protected function addEntityCommandSeparator(): self
    {
        $this->entityCommandCurrentGroupNumber++;

        return $this;
    }

    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendEntityCommandsToConfig(array &$config)
    {
        $this->appendCommandsToConfig(
            collect($this->entityCommandHandlers),
            $config
        );
    }

    public function entityCommandHandler(string $commandKey): ?EntityCommand
    {
        return isset($this->entityCommandHandlers[$commandKey])
            ? $this->entityCommandHandlers[$commandKey]
            : null;
    }
}