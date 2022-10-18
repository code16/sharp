<?php

namespace Code16\Sharp\Form\Eloquent\Relationships\Utils;

use Illuminate\Database\Eloquent\Model;

trait CanCreateRelatedModel
{
    protected function createRelatedModel($instance, string $attribute, array $data = []): Model
    {
        // Creation: we call the optional getDefaultAttributesFor($attribute)
        // on the model, to get some default values for required attributes
        $defaultAttributes = method_exists($instance, 'getDefaultAttributesFor')
            ? $instance->getDefaultAttributesFor($attribute)
            : [];

        $related = $instance->$attribute()->create(
            array_merge($defaultAttributes, $data),
        );

        // Force relation reload, in case there is more attributes to update in the request
        $instance->load($attribute);

        return $related;
    }
}
