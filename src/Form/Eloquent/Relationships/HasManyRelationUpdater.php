<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\SharpModelUpdater;

class HasManyRelationUpdater
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
        $relatedModelKeyName = $relatedModel->getKeyName();

        foreach($value as $itemData) {
            $id = $itemData[$relatedModelKeyName]["value"];

            $relatedInstance = $id
                ? $relatedModel->find($id)
                : $relatedModel->newInstance();

            app(SharpModelUpdater::class)->update($relatedInstance, $itemData);

            $relatedInstance->setAttribute(
                $foreignKeyName, $instance->id
            )->save();
        }
    }
}