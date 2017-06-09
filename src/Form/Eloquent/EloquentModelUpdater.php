<?php

namespace Code16\Sharp\Form\Eloquent;

use Code16\Sharp\Form\Eloquent\Request\UpdateRequestData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentModelUpdater
{

    /**
     * @param Model $instance
     * @param UpdateRequestData $data
     * @return Model
     */
    function update($instance, UpdateRequestData $data)
    {
        $delayedAttributes = [];
        $relationships = [];

        foreach($data->items() as $item) {

            // Only update referenced fields
            if(! $item->formField()) continue;

            if($this->isRelationship($instance, $item->attribute())) {
                $relationships[] = $item;

                continue;
            }

            try {
                $value = $item->formattedValue($instance);

            } catch(ModelNotFoundException $ex) {
                // We try to format a field which needs the instance to be persisted.
                // For example: the UploadFormatter needs a persisted instance if
                // its storagePath contains a parameter, like {id}. We delay the valuate.
                $delayedAttributes[] = $item;

                continue;
            }

            $this->valuateAttribute($instance, $item->attribute(), $value, $item->valuator());
        }

        // End of "normal" attributes.
        $instance->save();

        // Next, handle delayed attributes
        foreach($delayedAttributes as $dataItem) {
            $this->valuateAttribute(
                $instance, $dataItem->attribute(),
                $dataItem->formattedValue($instance),
                $dataItem->valuator()
            );
        }
        $instance->save();

        // Finally, handle relationships.
        $this->saveRelationships($instance, $relationships);

        return $instance;
    }

    /**
     * Valuates the $attribute with $value
     *
     * @param Model $instance
     * @param string $attribute
     * @param $value
     * @param null $valuator
     */
    protected function valuateAttribute($instance, string $attribute, $value, $valuator = null)
    {
        if ($valuator) {
            $value = $valuator->getValue(
                $instance, $attribute, $value
            );
        }

        $instance->setAttribute($attribute, $value);
    }

    /**
     * @param Model $instance
     * @param string $attribute
     * @return bool
     */
    protected function isRelationship($instance, $attribute): bool
    {
        return method_exists($instance, $attribute);
    }

    /**
     * @param Model $instance
     * @param array $relationships
     */
    protected function saveRelationships($instance, array $relationships)
    {
        foreach ($relationships as $dataItem) {
            $type = get_class($instance->{$dataItem->attribute()}());

            $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\\'
                . (new \ReflectionClass($type))->getShortName()
                . 'RelationUpdater');

            $relationshipUpdater->update(
                $instance, $dataItem->attribute(), $dataItem->formattedValue($instance)
            );
        }
    }
}