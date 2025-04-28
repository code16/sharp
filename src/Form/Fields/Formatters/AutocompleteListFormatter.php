<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormField;

class AutocompleteListFormatter extends SharpFieldFormatter implements PreparesForValidation
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
                    return [
                        $autocompleteField->itemIdAttribute() => null,
                    ];
                }

                return [
                    $autocompleteField->itemIdAttribute() => $autocompleteField->formatter()->fromFront(
                        $autocompleteField, $autocompleteField->key(), $item,
                    ),
                ];
            })
            ->all();
    }

    /**
     * @param  SharpFormAutocompleteListField  $field
     */
    public function prepareForValidation(SharpFormField $field, string $key, $value): array
    {
        $autocompleteField = $field->autocompleteField();

        return collect($value)
            ->map(fn ($item) => [
                $autocompleteField->key() => $item[$autocompleteField->itemIdAttribute()],
            ])
            ->all();
    }
}
