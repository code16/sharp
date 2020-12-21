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

    protected bool $layoutBuilt = false;
    protected array $sections = [];
    protected ?string $multiformAttribute = null;

    final public function showLayout(): array
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
    final public function instance($id): array
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
     * @param array $config
     * @return array
     */
    public function showConfig($instanceId, $config = []): array
    {
        $config = collect($config)
            ->merge([
                "multiformAttribute" => $this->multiformAttribute
            ])
            ->all();
        
        return tap($config, function(&$config) use($instanceId) {
            $this->appendEntityStateToConfig($config, $instanceId);
            $this->appendCommandsToConfig($config, $instanceId);
        });
    }

    protected function setMultiformAttribute(string $attribute): self
    {
        $this->multiformAttribute = $attribute;

        return $this;
    }

    private function buildFormFields(): void
    {
        $this->buildShowFields();
    }

    final protected function addSection(string $label, \Closure $callback = null): self
    {
        $this->layoutBuilt = false;

        $section = new ShowLayoutSection($label);
        $this->sections[] = $section;

        if($callback) {
            $callback($section);
        }

        return $this;
    }

    final protected function addEntityListSection(string $entityListKey, \Closure $callback = null): self
    {
        $this->layoutBuilt = false;

        $section = new ShowLayoutSection("");
        $section->addColumn(12, function($column) use($entityListKey) {
            $column->withSingleField($entityListKey);
        });

        if($callback) {
            $callback($section);
        }

        $this->sections[] = $section;

        return $this;
    }

    final protected function addEntityCommand(string $commandName, $commandHandlerOrClassName): void
    {
        throw new SharpException("Entity commands are not allowed in Show view");
    }

    /**
     * Build show config using ->addInstanceCommand() and ->setEntityState()
     *
     * @return void
     */
    function buildShowConfig(): void
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
    abstract function buildShowFields(): void;

    /**
     * Build form layout using ->addSection()
     *
     * @return void
     */
    abstract function buildShowLayout(): void;
}
