<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Show\Fields\SharpShowField;

class ListFormatter extends SharpShowFieldFormatter
{
    public function toFront(SharpShowField $field, $value)
    {
        return collect($value)
            ->map(function ($item) use ($field) {
                $field
                    ->itemFields()
                    ->each(function ($itemField) use ($item, &$itemArray) {
                        $key = $itemField->key();

                        if (str_contains($key, ':')) {
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
}
