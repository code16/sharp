<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class TextFormatter extends AbstractSimpleFormatter
{
    use HasMaybeLocalizedValue;
    
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $this->maybeLocalized($field, $value);
    }
}
