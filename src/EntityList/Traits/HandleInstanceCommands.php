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
        $this->getInstanceCommandsHandlers()
            ->each(function ($handlers, $positionKey) use (&$config, $instanceId) {
                $this->appendCommandsToConfig(
                    $handlers,
                    $config,
                    $positionKey,
                    $instanceId,
                );
            });
    }

    /**
     * Set the value of authorization key for instance commands and entity state,
     * which is an array of ids from the $items collection.
     */
    protected function addInstanceCommandsAuthorizationsToConfigForItems(array|Arrayable $items): void
    {
        $this->getInstanceCommandsHandlers()['instance']
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
            $this->instanceCommandHandlers = collect($this->getInstanceCommands())

                // First get commands which are section-based...
                ->filter(fn ($value, $index) => is_array($value))

                // ... and merge commands set for the whole instance
                ->merge(
                    [
                        'instance' => collect($this->getInstanceCommands())
                            ->filter(fn ($value, $index) => ! is_array($value)),
                    ]
                )

                ->map(function ($handlers) {
                    $groupIndex = 0;

                    return collect($handlers)
                        ->map(function ($handlerOrClassName, $commandKey) use (&$groupIndex) {
                            if (is_string($handlerOrClassName)) {
                                if (Str::startsWith($handlerOrClassName, '-')) {
                                    // It's a separator
                                    $groupIndex++;

                                    return null;
                                }
                                if (! class_exists($handlerOrClassName)) {
                                    throw new SharpException("Handler for instance command [{$handlerOrClassName}] is invalid");
                                }
                                $commandHandler = app($handlerOrClassName);
                            } else {
                                $commandHandler = $handlerOrClassName;
                            }

                            if (! $commandHandler instanceof InstanceCommand) {
                                throw new SharpException("Handler class for instance command [{$handlerOrClassName}] is not an subclass of ".InstanceCommand::class);
                            }

                            $commandHandler->setGroupIndex($groupIndex);
                            if (is_string($commandKey)) {
                                $commandHandler->setCommandKey($commandKey);
                            }

                            return $commandHandler;
                        })
                        ->filter()
                        ->values();
                });
        }

        return $this->instanceCommandHandlers;
    }

    final public function findInstanceCommandHandler(string $commandKey): ?InstanceCommand
    {
        return $this
            ->getInstanceCommandsHandlers()
            ->map(fn ($handlers, $positionKey) => $handlers)
            ->flatten()
            ->filter(function (InstanceCommand $command) use ($commandKey) {
                return $command->getCommandKey() === $commandKey;
            })
            ->first();
    }
}
