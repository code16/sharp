<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\EntityList\Traits\HandleInstanceCommands;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFields;
use Code16\Sharp\Utils\Traits\HandleCustomBreadcrumb;
use Code16\Sharp\Utils\Traits\HandleLocalizedFields;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpShow
{
    use WithCustomTransformers,
        HandleFields,
        HandleEntityState,
        HandleInstanceCommands,
        HandlePageAlertMessage,
        HandleCustomBreadcrumb,
        HandleLocalizedFields;

    protected ?ShowLayout $showLayout = null;
    protected ?string $multiformAttribute = null;
    protected ?SharpShowTextField $pageTitleField = null;

    final public function showLayout(): array
    {
        if ($this->showLayout === null) {
            $this->showLayout = new ShowLayout();
            $this->buildShowLayout($this->showLayout);
        }

        return $this->showLayout->toArray();
    }

    /**
     * Return the entity instance, as an array.
     */
    final public function instance(mixed $id): array
    {
        return collect($this->find($id))
            // Filter model attributes on actual show labels
            ->only(
                collect($this->getDataKeys())
                    ->when($this->breadcrumbAttribute, fn ($collect) => $collect->push($this->breadcrumbAttribute))
                    ->when($this->entityStateAttribute, fn ($collect) => $collect->push($this->entityStateAttribute))
                    ->when($this->multiformAttribute, fn ($collect) => $collect->push($this->multiformAttribute))
                    ->toArray()
            )
            ->all();
    }

    public function showConfig(mixed $instanceId, array $config = []): array
    {
        $config = collect($config)
            ->when($this->multiformAttribute, fn ($collection) => $collection->merge([
                'multiformAttribute' => $this->multiformAttribute,
            ]))
            ->when($this->pageTitleField, fn ($collection) => $collection->merge([
                'titleAttribute' => $this->pageTitleField->key,
            ]))
            ->all();

        return tap($config, function (&$config) use ($instanceId) {
            $this->appendBreadcrumbCustomLabelAttribute($config);
            $this->appendEntityStateToConfig($config, $instanceId);
            $this->appendInstanceCommandsToConfig($config, $instanceId);
            $this->appendGlobalMessageToConfig($config);
        });
    }

    final protected function configureMultiformAttribute(string $attribute): self
    {
        $this->multiformAttribute = $attribute;

        return $this;
    }

    final protected function configurePageTitleAttribute(string $attribute, bool $localized = false): self
    {
        $this->pageTitleField = SharpShowTextField::make($attribute)->setLocalized($localized);

        return $this;
    }

    private function buildFormFields(FieldsContainer $fields): void
    {
        $this->buildShowFields($fields);
    }

    /**
     * Build show config using ->addInstanceCommand() and ->setEntityState().
     */
    public function buildShowConfig(): void
    {
        // No default implementation
    }

    /**
     * Return all instance commands in an array of class names or instances.
     */
    public function getInstanceCommands(): ?array
    {
        return null;
    }

    /**
     * Retrieve a Model for the show and pack all its data as array.
     */
    abstract protected function find(mixed $id): array;

    /**
     * Build show fields.
     */
    abstract protected function buildShowFields(FieldsContainer $showFields): void;

    /**
     * Build show layout.
     */
    abstract protected function buildShowLayout(ShowLayout $showLayout): void;
}
