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

            $field->itemFields()->each(function($itemField) use($item, &$itemArray) {
                $key = $itemField->key();

                $value = ($key == '<item>')
                    ? $item // Special <item> case: the whole item should be passed to this field's formatter
                    : $item[$key] ?? null;

                if($value) {
                    $itemArray[$key] = $itemField->formatter()->toFront(
                        $itemField, $value
                    );
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
                    $formattedValue = $itemField->formatter()->fromFront($itemField, $key, $value);

                    if($key == '<item>') {
                        // Special case: the item represents the list item
                        $itemArray[$field->itemIdAttribute()] = $formattedValue;

                        // We can only have ONE field in the <item> case
                        break;
                    }

                    $itemArray[$key] = $formattedValue;
                }
            }

            return $itemArray;

        })->all();
    }
}