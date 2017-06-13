<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class HasOneRelationUpdater
{

    /**
     * @param $instance
     * @param string $attribute
     * @param $value
     */
    public function update($instance, $attribute, $value)
    {
        if(strpos($attribute, ":") !== false) {
            // This is a relation attribute update case (eg: mother:name)
            list($attribute, $subAttribute) = explode(":", $attribute);

            if($instance->$attribute) {
                $instance->$attribute()->update([
                    $subAttribute => $value
                ]);

            } else {
                $instance->$attribute()->create([
                    $subAttribute => $value
                ]);

                // Force relation reload, in case there is
                // more attributes to update in the request
                $instance->load($attribute);
            }

            return;
        }

        $relatedModel = $instance->$attribute()->getRelated();
        $foreignKeyName = $instance->$attribute()->getForeignKeyName();

        $relatedModel->find($value)->setAttribute(
            $foreignKeyName, $instance->id
        )->save();
    }
}