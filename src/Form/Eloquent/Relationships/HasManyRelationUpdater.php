<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\EloquentModelUpdater;
use Code16\Sharp\Form\Eloquent\Request\UpdateRequestData;

class HasManyRelationUpdater
{

    /**
     * @param $instance
     * @param string $attribute
     * @param array $value
     * @return bool
     */
    public function update($instance, $attribute, $value)
    {
        $relatedModel = $instance->$attribute()->getRelated();
        $foreignKeyName = $instance->$attribute()->getForeignKeyName();
        $relatedModelKeyName = $relatedModel->getKeyName();

        foreach($value as $item) {
            $id = $item->findItem($relatedModelKeyName)->value();

            $relatedInstance = $id
                ? $relatedModel->find($id)
                : $relatedModel->newInstance();

            app(EloquentModelUpdater::class)->update($relatedInstance, $item);

            $relatedInstance->setAttribute(
                $foreignKeyName, $instance->id
            )->save();
        }
    }
}