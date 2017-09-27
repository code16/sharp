<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class ListFormatter extends SharpFieldFormatter
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

            $field->itemFields()->each(function($itemField) use($item, &$itemArray) {
                $key = $itemField->key();

                if(isset($item[$key])) {
                    $itemArray[$key] = $itemField->formatter()
                        ->toFront($itemField, $item[$key]);
                }
            });

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
                    $itemArray[$key] = $itemField->formatter()
                        ->setInstanceId($this->instanceId)
                        ->fromFront($itemField, $key, $value);
                }
            }

            return $itemArray;

        })->all();
    }
}