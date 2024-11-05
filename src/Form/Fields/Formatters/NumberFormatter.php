<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class NumberFormatter extends SharpFieldFormatter
{
    /**
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        return (float) $value;
    }

    /**
     * @return mixed
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $this->toFront($field, $value);
    }
}
