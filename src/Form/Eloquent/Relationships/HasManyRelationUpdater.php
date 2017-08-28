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
     * @param array|null $sortConfiguration
     */
    public function update($instance, $attribute, $value, $sortConfiguration = null)
    {
        $relatedModel = $instance->$attribute()->getRelated();
        $relatedModelKeyName = $relatedModel->getKeyName();

        // Add / update sent items
        foreach((array)$value as $k => $item) {
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

            if($sortConfiguration) {
                $model->update([
                    $sortConfiguration["orderAttribute"] => ($k+1)
                ]);
            }

            $this->handledIds[] = $model->getAttribute($relatedModelKeyName);
        }

        // Remove unsent items
        $instance->$attribute()->whereNotIn($relatedModelKeyName, $this->handledIds)
            ->delete();
    }

    /**
     * @param array $item
     * @param $relatedModelKeyName
     * @return mixed
     */
    private function findItemId($item, $relatedModelKeyName)
    {
        $id = $item[$relatedModelKeyName];

        if($id) {
            $this->handledIds[] = $id;
        }

        return $id;
    }
}