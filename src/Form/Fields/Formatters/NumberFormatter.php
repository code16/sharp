<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class NumberFormatter extends SharpFieldFormatter
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

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $this->toFront($field, $value);
    }
}