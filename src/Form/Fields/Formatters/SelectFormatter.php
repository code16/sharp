<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class SelectFormatter extends SharpFieldFormatter
{
    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        if($field->multiple()) {
            return collect((array)$value)->map(function($item) use($field) {
                return is_array($item) || is_object($item)
                    ? ((array)$item)[$field->idAttribute()]
                    : $item;
            })->all();

        } elseif(is_array($value)) {
            // Strip other values is not configured to be multiple
            return $value[0];
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
        if($field->multiple()) {
            // We must transform items into associative arrays with the "id" key
            return collect((array)$value)->map(function ($item) {
                return ["id" => $item];
            })->all();

        } elseif(is_array($value)) {
            return $value[0];
        }

        return $value;
    }
}