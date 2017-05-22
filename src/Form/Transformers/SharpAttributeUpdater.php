<?php

namespace Code16\Sharp\Form\Transformers;

interface SharpAttributeUpdater
{

    /**
     * Update $instance->$attribute to $value.
     *
     * @param $instance
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    function update($instance, string $attribute, $value);
}