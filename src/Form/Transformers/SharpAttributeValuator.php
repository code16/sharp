<?php

namespace Code16\Sharp\Form\Transformers;

interface SharpAttributeValuator
{

    /**
     * Return the formatted value (based on the front sent $value)
     * for $instance->$attribute.
     *
     * @param $instance
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    function getValue($instance, string $attribute, $value);
}