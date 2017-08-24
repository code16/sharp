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

            } elseif($value) {
                $this->createRelatedModel(
                    $instance, $attribute, [$subAttribute => $value]
                );
            }

            return;
        }

        if(is_null($value)) {
            if($instance->$attribute) {
                $instance->$attribute()->delete();
            }

            return;
        }

        if(is_array($value)) {
            // We set more than one attribute on the related model
            if(is_null($instance->$attribute)) {
                $this->createRelatedModel(
                    $instance, $attribute, $value
                );

            } else {
                $instance->$attribute->update($value);
            }

            return;
        }

        $relatedModel = $instance->$attribute()->getRelated();
        $foreignKeyName = $instance->$attribute()->getForeignKeyName();

        $relatedModel->find($value)->setAttribute(
            $foreignKeyName, $instance->id
        )->save();
    }

    /**
     * @param $instance
     * @param $attribute
     * @param array $data
     */
    protected function createRelatedModel($instance, $attribute, $data = [])
    {
        // Creation: we call the optional getDefaultAttributesFor($attribute)
        // on the model, to get some default values for required attributes
        $defaultAttributes = method_exists($instance, 'getDefaultAttributesFor')
            ? $instance->getDefaultAttributesFor($attribute)
            : [];

        $instance->$attribute()->create(
            $defaultAttributes + $data
        );

        // Force relation reload, in case there is
        // more attributes to update in the request
        $instance->load($attribute);
    }
}