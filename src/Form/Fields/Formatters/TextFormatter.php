<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class TextFormatter extends AbstractSimpleFormatter
{
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if ($value !== null && $field->isLocalized()) {
            return collect(is_array($value) ? $value : [app()->getLocale() => $value])
                ->union(collect($this->dataLocalizations ?? [])->mapWithKeys(fn ($locale) => [$locale => null]))
                ->toArray();
        }

        return parent::fromFront($field, $attribute, $value);
    }
}
