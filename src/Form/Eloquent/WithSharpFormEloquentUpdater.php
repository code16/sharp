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
        // First transform data, passing false as a second parameter to allow partial objects.
        // This is important: this save() can be the second one called in the same request
        // for any field which formatter required a delay in his execution.
        $data = $this->applyTransformers($data, false);

        // Then handle manually ignored attributes...
        if (count($this->ignoredAttributes)) {
            $data = collect($data)
                ->filter(fn ($value, $attribute) => ! in_array($attribute, $this->ignoredAttributes))
                ->all();
        }

        // Finally call updater
        return app(EloquentModelUpdater::class)
            ->initRelationshipsConfiguration($this->getFormListFieldsConfiguration())
            ->update($instance, $data);
    }

    /**
     * Get all List fields which are sortable and their "orderAttribute"
     * configuration to be used by EloquentModelUpdater for automatic ordering.
     */
    protected function getFormListFieldsConfiguration(): Collection
    {
        return collect($this->fieldsContainer()->getFields())
            ->filter(function ($field) {
                return $field instanceof SharpFormListField
                    && $field->isSortable();
            })
            ->map(function ($listField) {
                return [
                    'key' => $listField->key(),
                    'orderAttribute' => $listField->orderAttribute(),
                ];
            })
            ->keyBy('key');
    }
}
