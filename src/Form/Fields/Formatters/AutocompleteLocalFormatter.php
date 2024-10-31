<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteLocalField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Transformers\ArrayConverter;

class AutocompleteLocalFormatter extends SharpFieldFormatter
{
    /**
     * @param SharpFormAutocompleteLocalField $field
     * @param $value
     * @return array|null
     */
    public function toFront(SharpFormField $field, $value)
    {
        $value = ArrayConverter::modelToArray($value);
        
        return is_null($value) || is_array($value)
            ? $value
            : [$field->itemIdAttribute() => $value];
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return is_array($value)
            ? $value[$field->itemIdAttribute()]
            : $value;
    }
}
