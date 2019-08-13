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
            return collect((array)$value)
                ->map(function($item) use($field) {

                    if(is_array($item)) {
                        return $item[$field->idAttribute()];
                    }

                    if(is_object($item)) {
                        if(method_exists($item, "toArray")) {
                            return $item->toArray()[$field->idAttribute()];
                        }

                        return ((array)$item)[$field->idAttribute()];
                    }

                    return $item;
                })
                ->all();

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
            return collect((array)$value)
                ->map(function ($item) use($field) {
                    return [$field->idAttribute() => $item];
                })
                ->all();

        } elseif(is_array($value)) {
            return $value[0];
        }

        return $value;
    }
}