<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class ListFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return collect($value)->map(function ($item) use($field) {
            $itemArray = [
                $field->itemIdAttribute() => $item[$field->itemIdAttribute()]
            ];

            foreach($item as $key => $value) {
                $itemField = $field->findItemFormFieldByKey($key);
                if($itemField) {
                    $itemArray[$key] = $itemField->formatter()->toFront($itemField, $value);
                }
            }

            return $itemArray;

        })->all();
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return array
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return collect($value)->map(function ($item) use($field) {
            $itemArray = [
                $field->itemIdAttribute() => $item[$field->itemIdAttribute()]
            ];

            foreach($item as $key => $value) {
                $itemField = $field->findItemFormFieldByKey($key);
                if($itemField) {
                    $itemArray[$key] = $itemField->formatter()->fromFront($itemField, $key, $value);
                }
            }

            return $itemArray;

        })->all();
    }
}