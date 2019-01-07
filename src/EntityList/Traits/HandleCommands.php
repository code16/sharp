<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Illuminate\Support\Collection;

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

    /**
     * Append the commands to the config returned to the front.
     *
     * @param array $config
     */
    protected function appendCommandsToConfig(array &$config)
    {
        foreach($this->commandHandlers as $commandName => $handler) {
            $formFields = $handler->form();
            $formLayout = $formFields ? $handler->formLayout() : null;
            $hasFormInitialData = $formFields ? $this->isInitialDataMethodImplemented($handler) : false;

            $config["commands"][] = [
                "key" => $commandName,
                "label" => $handler->label(),
                "type" => $handler->type(),
                "confirmation" => $handler->confirmationText() ?: null,
                "form" => $formFields ? [
                    "fields" => $formFields,
                    "layout" => $formLayout
                ] : null,
                "fetch_initial_data" => $hasFormInitialData,
                "authorization" => $handler->getGlobalAuthorization()
            ];
        }
    }

    /**
     * Set the value of authorization key for instance commands and entity state,
     * which is an array of ids from the $items collection.
     *
     * @param Collection $items
     */
    protected function addInstanceCommandsAuthorizationsToConfigForItems($items)
    {
        collect($this->commandHandlers)
            // Take all instance commands...
            ->filter(function($commandHandler) {
                return $commandHandler->type() == "instance" && $commandHandler->authorize();
            })
            // ... and Entity State if present...
            ->when($this->entityStateHandler, function(Collection $collection) {
                return $collection->push($this->entityStateHandler);
            })
            // ... and for each of them, set authorization for every $item
            ->each(function(InstanceCommand $commandHandler) use($items) {
                foreach ($items as $item) {
                    $commandHandler->checkAndStoreAuthorizationFor(
                        $item[$this->instanceIdAttribute]
                    );
                }
            });
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