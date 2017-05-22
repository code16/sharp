<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class UpdateBelongsToRelation
{

    /**
     * @param $instance
     * @param string $attribute
     * @param $value
     * @return bool
     */
    public function update($instance, $attribute, $value)
    {
        $instance->$attribute()->associate($value);

        return true;
    }
}