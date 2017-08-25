<?php

namespace Code16\Sharp\Form\Eloquent;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Illuminate\Database\Eloquent\Model;

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
        return app(EloquentModelUpdater::class)
            ->initRelationshipsConfiguration($this->getFormListFieldsConfiguration())
            ->update($instance, $this->applyTransformers($data, false));
    }

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