<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Traits\Utils\CommonCommandUtils;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

trait HandleInstanceCommands
{
    use CommonCommandUtils;
    
    protected array $instanceCommandHandlers = [];
    protected int $instanceCommandCurrentGroupNumber = 0;

    protected function addInstanceCommand(string $commandName, $commandHandlerOrClassName): self
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        if(!$commandHandler instanceof InstanceCommand) {
            throw new SharpException("Handler class for instance command [{$commandName}] is not an subclass of " . InstanceCommand::class);
        }

        $commandHandler->setGroupIndex($this->instanceCommandCurrentGroupNumber);

        $this->instanceCommandHandlers[$commandName] = $commandHandler;

        return $this;
    }

    protected function addInstanceCommandSeparator(): self
    {
        $this->instanceCommandCurrentGroupNumber++;

        return $this;
    }

    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendInstanceCommandsToConfig(array &$config, $instanceId = null): void
    {
        $this->appendCommandsToConfig(
            collect($this->instanceCommandHandlers), 
            $config, 
            $instanceId
        );
    }

    /**
     * Set the value of authorization key for instance commands and entity state,
     * which is an array of ids from the $items collection.
     *
     * @param array|Arrayable $items
     */
    protected function addInstanceCommandsAuthorizationsToConfigForItems($items)
    {
        collect($this->instanceCommandHandlers)
            // Take all authorized instance commands...
            ->filter(function($instanceCommandHandler) {
                return $instanceCommandHandler->authorize();
            })

            // ... and Entity State if present...
            ->when($this->entityStateHandler, function(Collection $collection) {
                return $collection->push($this->entityStateHandler);
            })

            // ... and for each of them, set authorization for every $item
            ->each(function($commandHandler) use($items) {
                foreach ($items as $item) {
                    $commandHandler->checkAndStoreAuthorizationFor(
                        $item[$this->instanceIdAttribute]
                    );
                }
            });
    }

    public function instanceCommandHandler(string $commandKey): ?InstanceCommand
    {
        return $this->instanceCommandHandlers[$commandKey] ?? null;
    }
}