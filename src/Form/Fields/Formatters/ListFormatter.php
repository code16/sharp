<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class ListFormatter extends SharpFieldFormatter
{
    /**
     * @param  SharpFormField  $field
     * @param  $value
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        return collect($value)
            ->map(function ($item) use ($field) {
                $itemArray = [
                    $field->itemIdAttribute() => $item[$field->itemIdAttribute()],
                ];

                $field
                    ->itemFields()
                    ->each(function ($itemField) use ($item, &$itemArray) {
                        $key = $itemField->key();

                        if (strpos($key, ':') !== false) {
                            // It's a sub attribute (like mother:name)
                            [$attribute, $subAttribute] = explode(':', $key);
                            $itemArray[$key] = isset($item[$attribute][$subAttribute])
                                ? $itemField->formatter()->toFront($itemField, $item[$attribute][$subAttribute])
                                : null;
                        } else {
                            $itemArray[$key] = isset($item[$key])
                                ? $itemField->formatter()->toFront($itemField, $item[$key])
                                : null;
                        }
                    });

                return $itemArray;
            })
            ->all();
    }

    /**
     * @param  SharpFormField  $field
     * @param  string  $attribute
     * @param  $value
     * @return array
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return collect($value)
            ->map(function ($item) use ($field) {
                $itemArray = [
                    $field->itemIdAttribute() => $item[$field->itemIdAttribute()],
                ];

                foreach ($item as $key => $value) {
                    $itemField = $field->findItemFormFieldByKey($key);

                    if ($itemField) {
                        $itemArray[$key] = $itemField->formatter()
                            ->setInstanceId($this->instanceId)
                            ->fromFront($itemField, $key, $value);
                    }
                }

                return $itemArray;
            })
            ->all();
    }
}
