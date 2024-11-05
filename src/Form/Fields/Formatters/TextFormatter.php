<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;

class TextFormatter extends AbstractSimpleFormatter
{
    /**
     * @param  SharpFormTextField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $this->maybeLocalized($field, $value);
    }
}
