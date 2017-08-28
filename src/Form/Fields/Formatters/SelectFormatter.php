<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class SelectFormatter implements SharpFieldFormatter
{
    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        if($field->multiple()) {
            return collect((array)$value)->map(function($item) {
                return ["id" => $item];
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
            return array_pluck($value, "id");

        } elseif(is_array($value)) {
            return $value[0]["id"];
        }

        return $value;
    }
}