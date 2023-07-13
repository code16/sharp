<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class AutocompleteListFormatter extends SharpFieldFormatter
{
    /**
     * @param  SharpFormField  $field
     * @param  $value
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        $autocompleteField = $field->autocompleteField();

        return collect($value)
            ->map(function ($item) use ($autocompleteField) {
                return [
                    $autocompleteField->itemIdAttribute() => $item[$autocompleteField->itemIdAttribute()],
                    $autocompleteField->key() => $autocompleteField->formatter()->toFront(
                        $autocompleteField, $item,
                    ),
                ];
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
        $autocompleteField = $field->autocompleteField();

        return collect($value)
            ->map(function ($item) use ($autocompleteField) {
                $item = $item[$autocompleteField->key()];

                return [
                    $autocompleteField->itemIdAttribute() => $autocompleteField->formatter()->fromFront(
                        $autocompleteField, $autocompleteField->key(), $item,
                    ),
                ];
            })
            ->all();
    }
}
