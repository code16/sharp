<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\EloquentModelUpdater;
use Code16\Sharp\Form\Eloquent\Request\UpdateRequestData;

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
     */
    public function update($instance, $attribute, $value)
    {
        $relatedModel = $instance->$attribute()->getRelated();
        $relatedModelKeyName = $relatedModel->getKeyName();

        // Add / update sent items
        foreach($value as $item) {
            $id = $this->findItemId($item, $relatedModelKeyName);
            $relatedInstance = $instance->$attribute()->findOrNew($id);

            if(!$relatedInstance->exists) {
                // Creation: we call the optional getDefaultAttributesFor($attribute)
                // on the model, to get some default values for required attributes
                $relatedInstance->fill(
                    method_exists($instance, 'getDefaultAttributesFor')
                        ? $instance->getDefaultAttributesFor($attribute)
                        : []
                );
            }

            $model = app(EloquentModelUpdater::class)->update($relatedInstance, $item);
            $this->handledIds[] = $model->getAttribute($relatedModelKeyName);
        }

        // Remove unsent items
        $instance->$attribute()->whereNotIn($relatedModelKeyName, $this->handledIds)
            ->delete();
    }

    /**
     * @param UpdateRequestData $item
     * @param $relatedModelKeyName
     * @return mixed
     */
    private function findItemId($item, $relatedModelKeyName)
    {
        $id = $item->findItem($relatedModelKeyName)->value();

        if($id) {
            $this->handledIds[] = $id;
        }

        return $id;
    }
}