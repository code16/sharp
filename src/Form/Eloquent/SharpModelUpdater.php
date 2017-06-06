<?php

namespace Code16\Sharp\Form\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SharpModelUpdater
{

    /**
     * @param Model $instance
     * @param array $data
     * @return bool
     */
    function update(Model $instance, array $data): bool
    {
        $delayedAttributes = [];
        $relationships = [];

        foreach($data as $attribute => $valueData) {

            $value = $valueData["value"];
            $valuator = $valueData["valuator"];
            $field = $valueData["field"];

            if(!$field) {
                continue;
            }

            $formatter = app('Code16\Sharp\Form\Eloquent\Formatters\\'
                . ucfirst($field->type()) . 'Formatter');

            if($this->isRelationship($instance, $attribute)) {
                $relationships[$attribute] = [
                    "relation_type" => get_class($instance->$attribute()),
                    "value" => $formatter->format($value, $field, $instance)
                ];

                continue;
            }

            try {
                $value = $formatter->format($value, $field, $instance);

            } catch(ModelNotFoundException $ex) {
                // We try to format a field which needs the instance to be persisted.
                // For example: the UploadFormatter needs a persisted instance if
                // its storagePath contains a parameter, like {id}. We delay the valuate.
                $delayedAttributes[$attribute] = $valueData;

                continue;
            }

            $this->valuateAttribute($instance, $attribute, $value, $valuator);
        }

        // End of "normal" attributes.
        $instance->save();

        // Next, handle delayed attributes
        foreach($delayedAttributes as $attribute => $valueData) {
            $value = $valueData["value"];
            $valuator = $valueData["valuator"];
            $field = $valueData["field"];
            $formatter = app('Code16\Sharp\Form\Eloquent\Formatters\\'
                . ucfirst($field->type()) . 'Formatter');

            $this->valuateAttribute(
                $instance, $attribute, $formatter->format($value, $field, $instance), $valuator
            );
        }
        $instance->save();

        // Finally, handle relationships.
        $this->saveRelationships($instance, $relationships);

        return true;
    }

    /**
     * Valuates the $attribute with $value
     *
     * @param Model $instance
     * @param string $attribute
     * @param $value
     * @param null $valuator
     */
    protected function valuateAttribute(Model $instance, string $attribute, $value, $valuator = null)
    {
        if ($valuator) {
            $instance->$attribute = $valuator->getValue(
                $instance, $attribute, $value
            );

            return;
        }

        $instance->$attribute = $value;
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
    protected function saveRelationships(Model $instance, array $relationships)
    {
        foreach ($relationships as $attribute => $relation) {
            $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\\'
                . (new \ReflectionClass($relation["relation_type"]))->getShortName()
                . 'RelationUpdater');

            $relationshipUpdater->update($instance, $attribute, $relation["value"]);
        }
    }
}