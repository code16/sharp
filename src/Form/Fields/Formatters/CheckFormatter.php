<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class CheckFormatter extends SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return bool
     */
    function toFront(SharpFormField $field, $value)
    {
        return (bool) $value;
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return bool
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if(is_string($value) && strlen($value)) {
            return !in_array($value, [
                "false", "0", "off"
            ]);
        }

        return !! $value;
    }
}