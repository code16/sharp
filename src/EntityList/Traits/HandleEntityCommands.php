<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Exceptions\SharpException;

trait HandleEntityCommands
{
    protected array $entityCommandHandlers = [];
    protected int $entityCommandCurrentGroupNumber = 0;
    protected ?string $primaryEntityCommandKey = null;

    protected function addEntityCommand(string $commandName, $commandHandlerOrClassName): self
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        if (! $commandHandler instanceof EntityCommand) {
            throw new SharpException("Handler class for entity command [{$commandName}] is not an subclass of ".EntityCommand::class);
        }

        $commandHandler->setGroupIndex($this->entityCommandCurrentGroupNumber);

        $this->entityCommandHandlers[$commandName] = $commandHandler;

        return $this;
    }

    protected function setPrimaryEntityCommand(string $commandName, $commandHandlerOrClassName): self
    {
        $this->addEntityCommand($commandName, $commandHandlerOrClassName);
        $this->primaryEntityCommandKey = $commandName;

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
            $config,
        );

        // If a command is defined as [primary], we have to update its config for the front:
        if ($this->primaryEntityCommandKey && $handler = $this->entityCommandHandler($this->primaryEntityCommandKey)) {
            foreach ($config['commands']['entity'][$handler->groupIndex()] as $index => $commandConfig) {
                if ($commandConfig['key'] === $this->primaryEntityCommandKey) {
                    $config['commands']['entity'][$handler->groupIndex()][$index]['primary'] = true;
                    break;
                }
            }
        }
    }

    public function entityCommandHandler(string $commandKey): ?EntityCommand
    {
        return $this->entityCommandHandlers[$commandKey] ?? null;
    }
}
