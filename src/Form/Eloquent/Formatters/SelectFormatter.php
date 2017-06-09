<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Fields\SharpFormSelectField;

class SelectFormatter
{
    /**
     * @param array|int $value
     * @param SharpFormSelectField $field
     * @return mixed
     */
    public function format($value, $field)
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
}