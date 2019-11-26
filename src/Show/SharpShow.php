<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Traits\HandleCommands;
use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Form\HandleFormFields;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpShow
{
    use WithCustomTransformers,
        HandleFormFields,
        HandleEntityState,
        HandleCommands;

    /** @var bool */
    protected $layoutBuilt = false;

    /** @var array */
    protected $sections = [];

    /**
     * @return array
     */
    public function showLayout()
    {
        if(!$this->layoutBuilt) {
            $this->buildShowLayout();
            $this->layoutBuilt = true;
        }

        return [
            "sections" => collect($this->sections)
                ->map->toArray()
                ->all()
        ];
    }

    /**
     * Return the entity instance, as an array.
     *
     * @param $id
     * @return array
     */
    function instance($id): array
    {
        return collect($this->find($id))
            // Filter model attributes on actual show labels
            ->only(
                array_merge(
                    $this->entityStateAttribute ? [$this->entityStateAttribute] : [],
                    $this->getDataKeys()
                )
            )
            ->all();
    }

    /**
     * Return the show config values (commands and state).
     *
     * @param $instanceId
     * @return array
     */
    function showConfig($instanceId): array
    {
        return tap([], function(&$config) use($instanceId) {
            $this->appendEntityStateToConfig($config, $instanceId);
            $this->appendCommandsToConfig($config, $instanceId);
        });
    }

    function buildFormFields()
    {
        $this->buildShowFields();
    }

    /**
     * @param string $label
     * @param \Closure|null $callback
     * @return $this
     */
    protected function addSection(string $label, \Closure $callback = null)
    {
        $this->layoutBuilt = false;

        $section = new ShowLayoutSection($label);
        $this->sections[] = $section;

        if($callback) {
            $callback($section);
        }

        return $this;
    }

    /**
     * @param string $label
     * @param string $entityListKey
     * @return $this
     */
    protected function addEntityListSection(string $label, string $entityListKey)
    {
        $this->layoutBuilt = false;

        $section = new ShowLayoutSection($label);
        $section->addColumn(12, function($column) use($entityListKey) {
            $column->withSingleField($entityListKey);
        });

        $this->sections[] = $section;

        return $this;
    }

    /**
     * @param string $commandName
     * @param string|EntityCommand $commandHandlerOrClassName
     * @throws SharpException
     */
    protected function addEntityCommand(string $commandName, $commandHandlerOrClassName)
    {
        throw new SharpException("Entity commands are not allowed in Show view");
    }

    /**
     * Build show config using ->addInstanceCommand() and ->setEntityState()
     *
     * @return void
     */
    function buildShowConfig()
    {
        // No default implementation
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    abstract function find($id): array;

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    abstract function buildShowFields();

    /**
     * Build form layout using ->addSection()
     *
     * @return void
     */
    abstract function buildShowLayout();
}