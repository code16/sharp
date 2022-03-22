<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Transformers\ArrayConverter;

class SelectFormatter extends SharpFieldFormatter
{
    /**
     * @param  SharpFormField  $field
     * @param $value
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        if ($field->multiple()) {
            return collect((array) $value)
                ->map(function ($item) use ($field) {
                    $item = ArrayConverter::modelToArray($item);

                    return is_array($item)
                        ? $item[$field->idAttribute()]
                        : $item;
                })
                ->all();
        } elseif (is_array($value)) {
            // Strip other values is not configured to be multiple
            return $value[0];
        }

        return $value;
    }

    /**
     * @param  SharpFormField  $field
     * @param  string  $attribute
     * @param $value
     * @return mixed
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if ($field->multiple()) {
            // We must transform items into associative arrays with the "id" key
            return collect((array) $value)
                ->map(function ($item) use ($field) {
                    return [$field->idAttribute() => $item];
                })
                ->all();
        } elseif (is_array($value)) {
            return $value[0];
        }

        return $value;
    }
}
