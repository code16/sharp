<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\EloquentModelUpdater;

class HasManyRelationUpdater
{
    /**
     * @var array
     */
    protected $handledIds = [];

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

        // Add / update sent items
        foreach($value as $item) {
            $id = $this->findItemId($item, $relatedModelKeyName);

            $relatedInstance = $id
                ? $relatedModel->find($id)
                : $relatedModel->newInstance();

            $relatedInstance->setAttribute(
                $foreignKeyName, $instance->id
            );

            $model = app(EloquentModelUpdater::class)->update($relatedInstance, $item);
            $this->handledIds[] = $model->getAttribute($relatedModelKeyName);
        }

        // Remove unsent items
        $relatedModel->where($foreignKeyName, $instance->id)
            ->whereNotIn($relatedModelKeyName, $this->handledIds)
            ->delete();
    }

    private function findItemId($item, $relatedModelKeyName)
    {
        $id = $item->findItem($relatedModelKeyName)->value();

        if($id) {
            $this->handledIds[] = $id;
        }

        return $id;
    }
}