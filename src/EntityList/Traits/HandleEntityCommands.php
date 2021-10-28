<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandleEntityCommands
{
    protected ?Collection $entityCommandHandlers = null;
    protected ?string $primaryEntityCommandKey = null;

    protected function configurePrimaryEntityCommand(string $commandKeyOrClassName): self
    {
        $this->primaryEntityCommandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return $this;
    }
    
    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendEntityCommandsToConfig(array &$config)
    {
        $this->appendCommandsToConfig(
            $this->getEntityCommandsHandlers(),
            $config
        );
        
        // If a command is defined as [primary], we have to update its config for the front:
        if($this->primaryEntityCommandKey && $handler = $this->findEntityCommandHandler($this->primaryEntityCommandKey)) {
            foreach($config["commands"]["entity"][$handler->groupIndex()] as $index => $commandConfig) {
                if($commandConfig["key"] === $this->primaryEntityCommandKey) {
                    $config["commands"]["entity"][$handler->groupIndex()][$index]["primary"] = true;
                    break;
                }
            }
        }
    }

    public final function getEntityCommandsHandlers(): Collection
    {
        if($this->entityCommandHandlers === null) {
            $groupIndex = 0;
            $this->entityCommandHandlers = collect($this->getEntityCommands())
                ->map(function($commandHandlerOrClassName, $commandKey) use (&$groupIndex) {
                    if(is_string($commandHandlerOrClassName)) {
                        if(Str::startsWith($commandHandlerOrClassName, "-")) {
                            // It's a separator
                            $groupIndex++;
                            return null;
                        }
                        if(!class_exists($commandHandlerOrClassName)) {
                            throw new SharpException("Handler for entity command [{$commandHandlerOrClassName}] is invalid");
                        }
                        $commandHandler = app($commandHandlerOrClassName);
                    } else {
                        $commandHandler = $commandHandlerOrClassName;
                    }

                    if(!$commandHandler instanceof EntityCommand) {
                        throw new SharpException("Handler class for entity command [{$commandHandlerOrClassName}] is not an subclass of " . EntityCommand::class);
                    }

                    $commandHandler->setGroupIndex($groupIndex);
                    if(is_string($commandKey)) {
                        $commandHandler->setCommandKey($commandKey);
                    }

                    if(isset($this->queryParams)) {
                        // We have to init query params of the command
                        $commandHandler->initQueryParams($this->queryParams);
                    }

                    return $commandHandler;
                })
                ->filter()
                ->values();
        }

        return $this->entityCommandHandlers;
    }

    public function findEntityCommandHandler(string $commandKey): ?EntityCommand
    {
        return $this
            ->getEntityCommandsHandlers()
            ->filter(function(EntityCommand $command) use ($commandKey) {
                return $command->getCommandKey() === $commandKey;
            })
            ->first();
    }
}