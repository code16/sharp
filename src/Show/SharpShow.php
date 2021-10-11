<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\EntityList\Traits\HandleInstanceCommands;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Utils\Fields\HandleFields;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Traits\HandleCustomBreadcrumb;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpShow
{
    use WithCustomTransformers,
        HandleFields,
        HandleEntityState,
        HandleInstanceCommands,
        HandlePageAlertMessage,
        HandleCustomBreadcrumb;

    protected ?ShowLayout $showLayout = null;
    protected ?string $multiformAttribute = null;

    final public function showLayout(): array
    {
        if($this->showLayout === null) {
            $this->showLayout = new ShowLayout();
            $this->buildShowLayout($this->showLayout);
        }

        return $this->showLayout->toArray();
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
            $this->appendGlobalMessageToConfig($config);
        });
    }

    protected final function configureMultiformAttribute(string $attribute): self
    {
        $this->multiformAttribute = $attribute;

        return $this;
    }

    private function buildFormFields(FieldsContainer $fields): void
    {
        $this->buildShowFields($fields);
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
     * Retrieve a Model for the show and pack all its data as array
     */
    abstract function find(mixed $id): array;

    /**
     * Build show fields
     */
    abstract function buildShowFields(FieldsContainer $showFields): void;

    /**
     * Build show layout
     */
    abstract function buildShowLayout(ShowLayout $showLayout): void;
}
