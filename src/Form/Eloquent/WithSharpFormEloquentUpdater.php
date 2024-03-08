<?php

namespace Code16\Sharp\Form\Eloquent;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait WithSharpFormEloquentUpdater
{
    protected array $ignoredAttributes = [];

    public function ignore(string|array $attribute): self
    {
        $this->ignoredAttributes = array_merge(
            $this->ignoredAttributes,
            (array) $attribute,
        );

        return $this;
    }

    /**
     * Update an Eloquent Model with $data (which is already Form Field formatted).
     */
    public function save(Model $instance, array $data): Model
    {
        $data = $this->applyTransformers($data, forceFullObject: false);

        // Handle manually ignored attributes
        if (count($this->ignoredAttributes)) {
            $data = collect($data)
                ->filter(fn ($value, $attribute) => ! in_array($attribute, $this->ignoredAttributes))
                ->all();
        }

        // Call updater
        return app(EloquentModelUpdater::class)
            ->initRelationshipsConfiguration($this->getFormListFieldsConfiguration())
            ->fillAfterUpdateWith(
                fn ($instanceId) => $this->formatDataAfterUpdate($data, $instanceId)
            )
            ->update($instance, $data);
    }

    /**
     * Get all List fields which are sortable and their "orderAttribute"
     * configuration to be used by EloquentModelUpdater for automatic ordering.
     */
    protected function getFormListFieldsConfiguration(): Collection
    {
        return collect($this->fieldsContainer()->getFields())
            ->filter(
                fn ($field) => $field instanceof SharpFormListField && $field->isSortable()
            )
            ->map(fn ($listField) => [
                'key' => $listField->key(),
                'orderAttribute' => $listField->orderAttribute(),
            ])
            ->keyBy('key');
    }
}
