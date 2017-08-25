<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class CheckFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

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