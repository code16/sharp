<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteListFormatter;

class SharpFormAutocompleteListField extends SharpFormListField
{
    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new AutocompleteListFormatter);
    }

    public function setItemField(SharpFormAutocompleteField $field): self
    {
        $this->itemFields = [$field];

        return $this;
    }

    public function addItemField(SharpFormField $field): self
    {
        if(!$field instanceof SharpFormAutocompleteField) {
            throw new \InvalidArgumentException("AutocompleteList item can only contain one field, and it must be a SharpFormAutocompleteField");
        }

        return $this->setItemField($field);
    }

    public function autocompleteField(): SharpFormAutocompleteField
    {
        return $this->itemFields()[0];
    }
}