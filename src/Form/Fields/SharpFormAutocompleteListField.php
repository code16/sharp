<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteListFormatter;

class SharpFormAutocompleteListField extends SharpFormListField
{
    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new AutocompleteListFormatter);
    }

    /**
     * @param SharpFormAutocompleteField $field
     * @return static
     */
    public function setItemField(SharpFormAutocompleteField $field)
    {
        $this->itemFields = [$field];

        return $this;
    }

    /**
     * @param SharpFormField $field
     * @return static
     */
    public function addItemField(SharpFormField $field)
    {
        if(!$field instanceof SharpFormAutocompleteField) {
            throw new \InvalidArgumentException("AutocompleteList item can only contain one field, and it must be a SharpFormAutocompleteField");
        }

        return $this->setItemField($field);
    }

    /**
     * @return SharpFormAutocompleteField
     */
    public function autocompleteField()
    {
        return $this->itemFields()[0];
    }
}