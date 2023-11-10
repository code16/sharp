<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class HtmlFormatter extends AbstractSimpleFormatter
{
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return null;
    }
}
