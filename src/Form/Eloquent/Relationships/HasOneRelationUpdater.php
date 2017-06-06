<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class HasOneRelationUpdater
{

    /**
     * @param $instance
     * @param string $attribute
     * @param $value
     * @return bool
     */
    public function update($instance, $attribute, $value)
    {
        $relatedModel = $instance->$attribute()->getRelated();
        $foreignKeyName = $instance->$attribute()->getForeignKeyName();

        $relatedModel->find($value)->setAttribute(
            $foreignKeyName, $instance->id
        )->save();
    }
}