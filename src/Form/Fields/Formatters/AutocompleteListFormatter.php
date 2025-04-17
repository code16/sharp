<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class AutocompleteListFormatter extends SharpFieldFormatter
{
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

    public function fromFront(SharpFormField $field, string $attribute, $value): array
    {
        $autocompleteField = $field->autocompleteField();

        return collect($value)
            ->map(function ($item) use ($autocompleteField) {
                $item = $item[$autocompleteField->key()] ?? null;

                if ($item === null) {
                    return null;
                }

                return [
                    $autocompleteField->itemIdAttribute() => $autocompleteField->formatter()->fromFront(
                        $autocompleteField, $autocompleteField->key(), $item,
                    ),
                ];
            })
            ->whereNotNull()
            ->all();
    }
}
