<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Traits\Utils\CommonCommandUtils;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandleInstanceCommands
{
    use CommonCommandUtils;

    private ?Collection $instanceCommandHandlers = null;

    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendInstanceCommandsToConfig(array &$config, $instanceId = null): void
    {
        $this->appendCommandsToConfig(
            $this->getInstanceCommandsHandlers(),
            $config,
            $instanceId,
        );
    }

    /**
     * Set the value of authorization key for instance commands and entity state,
     * which is an array of ids from the $items collection.
     *
     * @param  array|Arrayable  $items
     */
    protected function addInstanceCommandsAuthorizationsToConfigForItems($items)
    {
        $this->getInstanceCommandsHandlers()
            // Take all authorized instance commands...
            ->filter->authorize()

            // ... and Entity State if present...
            ->when($this->entityStateHandler, function (Collection $collection) {
                return $collection->push($this->entityStateHandler);
            })

            // ... and for each of them, set authorization for every $item
            ->each(function ($commandHandler) use ($items) {
                foreach ($items as $item) {
                    $commandHandler->checkAndStoreAuthorizationFor(
                        $item[$this->instanceIdAttribute],
                    );
                }
            });
    }

    final public function getInstanceCommandsHandlers(): Collection
    {
        if ($this->instanceCommandHandlers === null) {
            $groupIndex = 0;
            $this->instanceCommandHandlers = collect($this->getInstanceCommands())
                ->map(function ($commandHandlerOrClassName, $commandKey) use (&$groupIndex) {
                    if (is_string($commandHandlerOrClassName)) {
                        if (Str::startsWith($commandHandlerOrClassName, '-')) {
                            // It's a separator
                            $groupIndex++;

                            return null;
                        }
                        if (! class_exists($commandHandlerOrClassName)) {
                            throw new SharpException("Handler for instance command [{$commandHandlerOrClassName}] is invalid");
                        }
                        $commandHandler = app($commandHandlerOrClassName);
                    } else {
                        $commandHandler = $commandHandlerOrClassName;
                    }

                    if (! $commandHandler instanceof InstanceCommand) {
                        throw new SharpException("Handler class for instance command [{$commandHandlerOrClassName}] is not an subclass of ".InstanceCommand::class);
                    }

                    $commandHandler->setGroupIndex($groupIndex);
                    if (is_string($commandKey)) {
                        $commandHandler->setCommandKey($commandKey);
                    }

                    return $commandHandler;
                })
                ->filter()
                ->values();
        }

        return $this->instanceCommandHandlers;
    }

    final public function findInstanceCommandHandler(string $commandKey): ?InstanceCommand
    {
        return $this
            ->getInstanceCommandsHandlers()
            ->filter(function (InstanceCommand $command) use ($commandKey) {
                return $command->getCommandKey() === $commandKey;
            })
            ->first();
    }
}
