<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\EntityList\Traits\Utils\CommonCommandUtils;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandleDashboardCommands
{
    use CommonCommandUtils;

    protected ?Collection $dashboardCommandHandlers = null;

    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendDashboardCommandsToConfig(array &$config): void
    {
        $this->getDashboardCommandHandlers()
            ->each(function ($handlers, $positionKey) use (&$config) {
                $this->appendCommandsToConfig(
                    $handlers,
                    $config,
                    $positionKey,
                );
            });
    }

    final public function getDashboardCommandHandlers(): Collection
    {
        if ($this->dashboardCommandHandlers === null) {
            $this->dashboardCommandHandlers = collect($this->getDashboardCommands())

                // First get commands which are section-based...
                ->filter(fn ($value, $index) => is_array($value))

                // ... and merge commands set for the whole dashboard
                ->merge(
                    [
                        'dashboard' => collect($this->getDashboardCommands())
                            ->filter(fn ($value, $index) => ! is_array($value)),
                    ]
                )
                
                ->map(function ($handlers) {
                    $groupIndex = 0;

                    return collect($handlers)
                        ->map(function ($commandHandlerOrClassName, $commandKey) use (&$groupIndex) {
                            if (is_string($commandHandlerOrClassName)) {
                                if (Str::startsWith($commandHandlerOrClassName, '-')) {
                                    // It's a separator
                                    $groupIndex++;

                                    return null;
                                }
                                if (!class_exists($commandHandlerOrClassName)) {
                                    throw new SharpException("Handler for dashboard command [{$commandHandlerOrClassName}] is invalid");
                                }
                                $commandHandler = app($commandHandlerOrClassName);
                            } else {
                                $commandHandler = $commandHandlerOrClassName;
                            }

                            if (!$commandHandler instanceof DashboardCommand) {
                                throw new SharpException("Handler class for dashboard command [{$commandHandlerOrClassName}] is not an subclass of " . DashboardCommand::class);
                            }

                            $commandHandler->setGroupIndex($groupIndex);
                            if (is_string($commandKey)) {
                                $commandHandler->setCommandKey($commandKey);
                            }

                            if (isset($this->queryParams)) {
                                // We have to init query params of the command
                                $commandHandler->initQueryParams($this->queryParams);
                            }

                            return $commandHandler;
                        })
                        ->filter()
                        ->values();
                });
        }

        return $this->dashboardCommandHandlers;
    }

    public function findDashboardCommandHandler(string $commandKey): ?DashboardCommand
    {
        return $this
            ->getDashboardCommandHandlers()
            ->map(fn ($handlers, $positionKey) => $handlers)
            ->flatten()
            ->filter(fn (DashboardCommand $command) => $command->getCommandKey() === $commandKey)
            ->first();
    }
}
