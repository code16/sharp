<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\EntityList\Commands\EntityCommand;

trait HandleDashboardCommands
{
    /** @var array */
    protected $dashboardCommandHandlers = [];

    /** @var int */
    protected $dashboardCommandCurrentGroupNumber = 0;

    /**
     * @param string $commandName
     * @param string|DashboardCommand $commandHandlerOrClassName
     * @return $this
     */
    protected function addDashboardCommand(string $commandName, $commandHandlerOrClassName)
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        $commandHandler->setGroupIndex($this->dashboardCommandCurrentGroupNumber);

        $this->dashboardCommandHandlers[$commandName] = $commandHandler;

        return $this;
    }

    /**
     * @return $this
     */
    protected function addDashboardCommandSeparator()
    {
        $this->dashboardCommandCurrentGroupNumber++;

        return $this;
    }

    /**
     * Append the commands to the config returned to the front.
     *
     * @param array $config
     */
    protected function appendCommandsToConfig(array &$config)
    {
        collect($this->dashboardCommandHandlers)
            ->each(function($handler, $commandName) use(&$config) {
                $formFields = $handler->form();
                $formLayout = $formFields ? $handler->formLayout() : null;
                $hasFormInitialData = $formFields ? $this->isInitialDataMethodImplemented($handler) : false;

                $config["commands"][$handler->type()][$handler->groupIndex()][] = [
                    "key" => $commandName,
                    "label" => $handler->label(),
                    "description" => $handler->description(),
                    "type" => $handler->type(),
                    "confirmation" => $handler->confirmationText() ?: null,
                    "form" => $formFields ? [
                        "fields" => $formFields,
                        "layout" => $formLayout
                    ] : null,
                    "fetch_initial_data" => $hasFormInitialData,
                    "authorization" => $handler->getGlobalAuthorization()
                ];
            });
    }

    /**
     * @param string $commandKey
     * @return EntityCommand|null
     */
    public function dashboardCommandHandler(string $commandKey)
    {
        return isset($this->dashboardCommandHandlers[$commandKey])
            ? $this->dashboardCommandHandlers[$commandKey]
            : null;
    }

    /**
     * @param $handler
     * @return bool
     */
    private function isInitialDataMethodImplemented($handler)
    {
        try {
            $foo = new \ReflectionMethod(get_class($handler), 'initialData');
            $declaringClass = $foo->getDeclaringClass()->getName();

            return $foo->getPrototype()->getDeclaringClass()->getName() !== $declaringClass;

        } catch (\ReflectionException $e) {
            return false;
        }
    }
}