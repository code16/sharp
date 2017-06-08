<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class BelongsToManyRelationUpdater
{
    /**
     * @var array
     */
    protected $handledIds = [];

    /**
     * @param $instance
     * @param string $attribute
     * @param array $value
     * @return bool
     */
    public function update($instance, $attribute, $value)
    {
//        dd($instance, $attribute, $value);

        $instance->$attribute()->sync($value);
    }

}