<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class NumberFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return (int)$value;
    }

    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $this->toFront($field, $value);
    }
}