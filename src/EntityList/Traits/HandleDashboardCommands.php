<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\EntityList\Traits\Utils\CommonCommandUtils;

trait HandleDashboardCommands
{
    use CommonCommandUtils;
    
    protected array $dashboardCommandHandlers = [];
    protected int $dashboardCommandCurrentGroupNumber = 0;

    protected function addDashboardCommand(string $commandName, $commandHandlerOrClassName): self
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        $commandHandler->setGroupIndex($this->dashboardCommandCurrentGroupNumber);

        $this->dashboardCommandHandlers[$commandName] = $commandHandler;

        return $this;
    }

    protected function addDashboardCommandSeparator(): self
    {
        $this->dashboardCommandCurrentGroupNumber++;

        return $this;
    }

    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendDashboardCommandsToConfig(array &$config)
    {
        $this->appendCommandsToConfig(
            collect($this->dashboardCommandHandlers),
            $config
        );
    }

    public function dashboardCommandHandler(string $commandKey): ?DashboardCommand
    {
        return isset($this->dashboardCommandHandlers[$commandKey])
            ? $this->dashboardCommandHandlers[$commandKey]
            : null;
    }
}