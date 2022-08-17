<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\Utils\CanCreateRelatedModel;

class HasOneRelationUpdater
{
    use CanCreateRelatedModel;

    public function update(object $instance, string $attribute, $value)
    {
        if (strpos($attribute, ':') !== false) {
            // This is a relation attribute update case (eg: mother:name)
            [$attribute, $subAttribute] = explode(':', $attribute);

            if ($instance->$attribute) {
                $instance->$attribute->$subAttribute = $value;
                $instance->$attribute->save();
            } elseif ($value) {
                $this->createRelatedModel(
                    $instance, $attribute, [$subAttribute => $value],
                );
            }

            return;
        }

        if (is_null($value)) {
            if ($instance->$attribute) {
                $instance->$attribute()->delete();
            }

            return;
        }

        if (is_array($value)) {
            // We set more than one attribute on the related model
            if (is_null($instance->$attribute)) {
                $this->createRelatedModel(
                    $instance, $attribute, $value,
                );
            } else {
                $instance->$attribute->update($value);
            }

            return;
        }

        $relatedModel = $instance->$attribute()->getRelated();
        $foreignKeyName = $instance->$attribute()->getForeignKeyName();

        $relatedModel->find($value)->setAttribute(
            $foreignKeyName, $instance->id,
        )->save();
    }
}
