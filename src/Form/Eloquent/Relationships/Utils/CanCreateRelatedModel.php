<?php

namespace Code16\Sharp\Form\Eloquent\Relationships\Utils;

trait CanCreateRelatedModel
{
    /**
     * @param $instance
     * @param $attribute
     * @param  array  $data
     * @return mixed
     */
    protected function createRelatedModel($instance, $attribute, $data = [])
    {
        // Creation: we call the optional getDefaultAttributesFor($attribute)
        // on the model, to get some default values for required attributes
        $defaultAttributes = method_exists($instance, 'getDefaultAttributesFor')
            ? $instance->getDefaultAttributesFor($attribute)
            : [];

        $related = $instance->$attribute()->create(
            array_merge($defaultAttributes, $data),
        );

        // Force relation reload, in case there is
        // more attributes to update in the request
        $instance->load($attribute);

        return $related;
    }
}
