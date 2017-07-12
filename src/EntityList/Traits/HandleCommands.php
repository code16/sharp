<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

trait HandleCommands
{
    /**
     * @var array
     */
    protected $commandHandlers = [];

    /**
     * @param string $commandName
     * @param string|EntityCommand $commandHandler
     * @return $this
     */
    protected function addEntityCommand(string $commandName, $commandHandler)
    {
        $this->addCommand($commandName, $commandHandler);

        return $this;
    }

    /**
     * @param string $commandName
     * @param string|InstanceCommand $commandHandler
     * @return $this
     */
    protected function addInstanceCommand(string $commandName, $commandHandler)
    {
        $this->addCommand($commandName, $commandHandler);

        return $this;
    }

    protected function appendCommandsToConfig(array &$config)
    {
        foreach($this->commandHandlers as $commandName => $handler) {
            $formFields = $handler->form();
            $formLayout = $formFields ? $handler->formLayout() : null;

            $config["commands"][] = [
                "key" => $commandName,
                "label" => $handler->label(),
                "type" => $handler->type(),
                "confirmation" => $handler->confirmationText() ?: null,
                "form" => $formFields ? [
                    "fields" => $formFields,
                    "layout" => $formLayout
                ] : null,
                "authorization" => $handler->authorize()
            ];
        }
    }

    /**
     * @param string $commandKey
     * @return EntityCommand|null
     */
    public function entityCommandHandler(string $commandKey)
    {
        return isset($this->commandHandlers[$commandKey])
                && $this->commandHandlers[$commandKey]->type() == "entity"
            ? $this->commandHandlers[$commandKey]
            : null;
    }

    /**
     * @param string $commandKey
     * @return InstanceCommand|null
     */
    public function instanceCommandHandler(string $commandKey)
    {
        return isset($this->commandHandlers[$commandKey])
        && $this->commandHandlers[$commandKey]->type() == "instance"
            ? $this->commandHandlers[$commandKey]
            : null;
    }

    /**
     * @param string $commandName
     * @param string|Command $commandHandler
     * @return $this
     */
    private function addCommand(string $commandName, $commandHandler)
    {
        $this->commandHandlers[$commandName] = $commandHandler instanceof Command
            ? $commandHandler
            : app($commandHandler);

        return $this;
    }
}