<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class AutocompleteFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        if(is_object($value) || is_array($value)) {
            // A structure has been passed: we have to convert it
            if($field->isRemote()) {
                return [
                    "id" => $value[$field->itemIdAttribute()],
                    "label" => $value[$field->itemLabelAttribute()]
                ];
            }

            return $value[$field->itemIdAttribute()];
        }

        return $value;
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $value;
    }
}