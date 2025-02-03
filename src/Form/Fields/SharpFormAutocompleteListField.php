<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteListFormatter;
use Code16\Sharp\Form\Fields\Utils\IsSharpFormAutocompleteField;

class SharpFormAutocompleteListField extends SharpFormListField
{
    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new AutocompleteListFormatter());
    }

    public function setItemField(IsSharpFormAutocompleteField $field): self
    {
        $this->itemFields = [$field];

        return $this;
    }

    public function addItemField(SharpFormField $field): self
    {
        if (! $field instanceof IsSharpFormAutocompleteField) {
            throw new \InvalidArgumentException('AutocompleteList item can only contain one field, and it must be SharpFormAutocompleteRemoteField or SharpFormAutocompleteLocalField');
        }

        return $this->setItemField($field);
    }

    public function autocompleteField(): IsSharpFormAutocompleteField
    {
        return $this->itemFields()[0];
    }
}
