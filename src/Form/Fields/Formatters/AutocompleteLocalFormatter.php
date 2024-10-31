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
        
//        if(is_null($value)) {
//            return null;
//        }
//
//        if(is_array($value)) {
//            $selectedValue = isset($value[$field->itemIdAttribute()])
//                ? collect($field->localValues())
//                    ->firstWhere($field->itemIdAttribute(), $value[$field->itemIdAttribute()])
//                : null;
//        } else {
//            $selectedValue = collect($field->localValues())->firstWhere($field->itemIdAttribute(), $value);
//        }
//
//        return $selectedValue
//            ? [
//                ...$selectedValue,
//                '_html' => $field->renderResultItem($selectedValue),
//            ]
//            : null;
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return is_array($value)
            ? $value[$field->itemIdAttribute()]
            : $value;
    }
}
