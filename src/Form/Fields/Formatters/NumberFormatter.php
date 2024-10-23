<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class NumberFormatter extends SharpFieldFormatter
{
    public function toFront(SharpFormField $field, $value)
    {
        return (float) $value;
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return (float) $value;
    }
}
