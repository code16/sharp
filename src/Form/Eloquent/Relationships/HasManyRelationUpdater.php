<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\EloquentModelUpdater;

class HasManyRelationUpdater
{
    protected array $handledIds = [];

    public function update(object $instance, string $attribute, array $value, ?array $sortConfiguration = null)
    {
        $relatedModel = $instance->$attribute()->getRelated();
        $relatedModelKeyName = $relatedModel->getKeyName();

        // Add / update sent items
        foreach ((array) $value as $k => $item) {
            $id = $this->findItemId($item, $relatedModelKeyName);
            $relatedInstance = $instance->$attribute()->findOrNew($id);

            if (!$relatedInstance->exists) {
                // Creation: we call the optional getDefaultAttributesFor($attribute)
                // on the model, to get some default values for required attributes
                $relatedInstance->fill(
                    method_exists($instance, 'getDefaultAttributesFor')
                        ? $instance->getDefaultAttributesFor($attribute)
                        : []
                );
            }

            if ($relatedInstance->incrementing) {
                // Remove the id
                unset($item[$relatedModelKeyName]);
            }

            $model = app(EloquentModelUpdater::class)->update($relatedInstance, $item);

            if ($sortConfiguration) {
                $model->update([
                    $sortConfiguration['orderAttribute'] => ($k + 1),
                ]);
            }

            $this->handledIds[] = $model->getAttribute($relatedModelKeyName);
        }

        // Remove unsent items
        $instance->$attribute->whereNotIn($relatedModelKeyName, $this->handledIds)
            ->each(function ($item) {
                $item->delete();
            });
    }

    private function findItemId(array $item, string $relatedModelKeyName)
    {
        $id = $item[$relatedModelKeyName];

        if ($id) {
            $this->handledIds[] = $id;
        }

        return $id;
    }
}
