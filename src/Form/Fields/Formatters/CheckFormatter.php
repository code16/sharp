<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class CheckFormatter extends SharpFieldFormatter
{
    public function toFront(SharpFormField $field, $value)
    {
        return (bool) $value;
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if (is_string($value) && strlen($value)) {
            return ! in_array($value, [
                'false', '0', 'off',
            ]);
        }

        return (bool) $value;
    }
}
