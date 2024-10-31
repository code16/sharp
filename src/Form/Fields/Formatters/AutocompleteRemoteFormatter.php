<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Transformers\ArrayConverter;

class AutocompleteRemoteFormatter extends SharpFieldFormatter
{
    /**
     * @param SharpFormAutocompleteRemoteField $field
     * @param $value
     * @return array|null
     */
    public function toFront(SharpFormField $field, $value)
    {
        $value = ArrayConverter::modelToArray($value);
        
        if(is_null($value)) {
            return null;
        }
        
        if(is_array($value)) {
            return $field->itemWithRenderedTemplates($value);
        }
        
        return [$field->itemIdAttribute() => $value];
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return is_array($value)
            ? $value[$field->itemIdAttribute()]
            : $value;
    }
}
