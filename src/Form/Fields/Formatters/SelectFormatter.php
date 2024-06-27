<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Utils\Transformers\ArrayConverter;

class SelectFormatter extends SharpFieldFormatter
{
    /**
     * @param SharpFormSelectField $field
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
        }

        return is_array($value) ? $value[0] : $value;
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if ($field->multiple()) {
            // We must transform items into associative arrays with the "id" key
            return collect((array) $value)
                ->map(function ($item) use ($field) {
                    return [$field->idAttribute() => $item];
                })
                ->all();
        }

        return is_array($value) ? $value[0] : $value;
    }
}
