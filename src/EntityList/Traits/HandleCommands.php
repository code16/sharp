<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Illuminate\Support\Collection;

trait HandleCommands
{
    /** @var array */
    protected $entityCommandHandlers = [];

    /** @var array */
    protected $instanceCommandHandlers = [];

    /** @var int */
    protected $instanceCommandCurrentGroupNumber = 0;

    /** @var int */
    protected $entityCommandCurrentGroupNumber = 0;

    /**
     * @param string $commandName
     * @param string|EntityCommand $commandHandlerOrClassName
     * @return $this
     */
    protected function addEntityCommand(string $commandName, $commandHandlerOrClassName)
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        $commandHandler->setGroupIndex($this->entityCommandCurrentGroupNumber);

        $this->entityCommandHandlers[$commandName] = $commandHandler;

        return $this;
    }

    /**
     * @param string $commandName
     * @param string|InstanceCommand $commandHandlerOrClassName
     * @return $this
     */
    protected function addInstanceCommand(string $commandName, $commandHandlerOrClassName)
    {
        $commandHandler = is_string($commandHandlerOrClassName)
            ? app($commandHandlerOrClassName)
            : $commandHandlerOrClassName;

        $commandHandler->setGroupIndex($this->instanceCommandCurrentGroupNumber);

        $this->instanceCommandHandlers[$commandName] = $commandHandler;

        return $this;
    }

    /**
     * @return $this
     */
    protected function addInstanceCommandSeparator()
    {
        $this->instanceCommandCurrentGroupNumber++;

        return $this;
    }

    /**
     * @return $this
     */
    protected function addEntityCommandSeparator()
    {
        $this->entityCommandCurrentGroupNumber++;

        return $this;
    }

    /**
     * Append the commands to the config returned to the front.
     *
     * @param array $config
     */
    protected function appendCommandsToConfig(array &$config)
    {
        collect($this->entityCommandHandlers)
            ->merge(collect($this->instanceCommandHandlers))
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
     * Set the value of authorization key for instance commands and entity state,
     * which is an array of ids from the $items collection.
     *
     * @param Collection $items
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

    /**
     * @param string $commandKey
     * @return EntityCommand|null
     */
    public function entityCommandHandler(string $commandKey)
    {
        return isset($this->entityCommandHandlers[$commandKey])
            ? $this->entityCommandHandlers[$commandKey]
            : null;
    }

    /**
     * @param string $commandKey
     * @return InstanceCommand|null
     */
    public function instanceCommandHandler(string $commandKey)
    {
        return isset($this->instanceCommandHandlers[$commandKey])
            ? $this->instanceCommandHandlers[$commandKey]
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