<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\EntityList\Traits\HandleInstanceCommands;
use Code16\Sharp\Utils\Fields\HandleFields;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Traits\HandleCustomBreadcrumb;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpShow
{
    use WithCustomTransformers,
        HandleFields,
        HandleEntityState,
        HandleInstanceCommands,
        HandleCustomBreadcrumb;

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
     */
    public final function instance(mixed $id): array
    {
        return collect($this->find($id))
            // Filter model attributes on actual show labels
            ->only(
                array_merge(
                    $this->breadcrumbAttribute ? [$this->breadcrumbAttribute] : [],
                    $this->entityStateAttribute ? [$this->entityStateAttribute] : [],
                    $this->getDataKeys()
                )
            )
            ->all();
    }

    /**
     * Return the show config values (commands and state).
     */
    public function showConfig(mixed $instanceId, array $config = []): array
    {
        $config = collect($config)
            ->merge([
                "multiformAttribute" => $this->multiformAttribute
            ])
            ->all();
        
        return tap($config, function(&$config) use($instanceId) {
            $this->appendBreadcrumbCustomLabelAttribute($config);
            $this->appendEntityStateToConfig($config, $instanceId);
            $this->appendInstanceCommandsToConfig($config, $instanceId);
        });
    }

    protected final function setMultiformAttribute(string $attribute): self
    {
        $this->multiformAttribute = $attribute;

        return $this;
    }

    private function buildFormFields(FieldsContainer $fields): void
    {
        $this->buildShowFields($fields);
    }

    protected final function addSection(string $label, \Closure $callback = null): self
    {
        $this->layoutBuilt = false;

        $section = new ShowLayoutSection($label);
        $this->sections[] = $section;

        if($callback) {
            $callback($section);
        }

        return $this;
    }

    protected final function addEntityListSection(string $entityListKey, \Closure $callback = null): self
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

    /**
     * Build show config using ->addInstanceCommand() and ->setEntityState()
     */
    function buildShowConfig(): void
    {
        // No default implementation
    }

    /**
     * Return all instance commands in an array of class names or instances
     */
    function getInstanceCommands(): ?array
    {
        return null;
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     */
    abstract function find(mixed $id): array;

    /**
     * Build form fields using ->addField()
     */
    abstract function buildShowFields(FieldsContainer $showFields): void;

    /**
     * Build form layout using ->addSection()
     */
    abstract function buildShowLayout(): void;
}
