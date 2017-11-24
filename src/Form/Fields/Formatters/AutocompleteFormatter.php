<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class AutocompleteFormatter extends SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return is_null($value) || is_array($value)
            ? $value
            : [$field->itemIdAttribute() => $value];
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return is_null($value) || is_array($value)
            ? $value[$field->itemIdAttribute()]
            : $value;
    }
}