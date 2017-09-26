<?php

namespace Code16\Sharp\Form\Eloquent;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait WithSharpFormEloquentUpdater
{

    /**
     * @var array
     */
    protected $ignoredAttributes = [];

    /**
     * @param string|array $attribute
     * @return $this
     */
    function ignore($attribute)
    {
        $this->ignoredAttributes += (array)$attribute;

        return $this;
    }

    /**
     * Update an Eloquent Model with $data (which is already Form Field formatted)
     *
     * @param Model $instance
     * @param array $data
     * @return Model
     */
    function save(Model $instance, array $data)
    {
        // First transform data...
        $data = $this->applyTransformers($data, false);

        // Then handle manually ignored attributes...
        if(count($this->ignoredAttributes)) {
            $data = collect($data)->filter(function ($value, $attribute) {
                return array_search($attribute, $this->ignoredAttributes) === false;
            })->all();
        }

        // Finally call updater
        return app(EloquentModelUpdater::class)
            ->initRelationshipsConfiguration($this->getFormListFieldsConfiguration())
            ->update($instance, $data);
    }

    /**
     * Get all List fields which are sortable and their "orderAttribute"
     * configuration to be used by EloquentModelUpdater
     * for automatic ordering.
     *
     * @return Collection
     */
    protected function getFormListFieldsConfiguration()
    {
        return collect($this->fields)
            ->filter(function($field) {
                return $field instanceof SharpFormListField
                    && $field->isSortable();

            })->map(function($listField) {
                return [
                    "key" => $listField->key(),
                    "orderAttribute" => $listField->orderAttribute()
                ];

            })->keyBy("key");
    }
}