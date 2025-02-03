<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

abstract class AbstractSimpleFormatter extends SharpFieldFormatter
{
    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $value;
    }
}
