<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;

class SelectFormatter implements SharpFieldFormatter
{
    /**
     * @param array|int $value
     * @param SharpFormSelectField $field
     * @return mixed
     */
    public function format($value, $field)
    {

    }

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

    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        // TODO check this (maybe create array if multiple, ...)
        return $value;
    }
}