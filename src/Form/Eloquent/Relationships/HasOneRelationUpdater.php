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
                $instance->$attribute->$subAttribute = $value;
                $instance->$attribute->save();

            } else {
                // Creation: we call the optional getDefaultAttributesFor($attribute)
                // on the model, to get some default values for required attributes
                $defaultAttributes = method_exists($instance, 'getDefaultAttributesFor')
                    ? $instance->getDefaultAttributesFor($attribute)
                    : [];

                $instance->$attribute()->create(
                    $defaultAttributes + [$subAttribute => $value]
                );

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