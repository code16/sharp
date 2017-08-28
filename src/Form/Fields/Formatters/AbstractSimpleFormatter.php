<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

abstract class AbstractSimpleFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $value;
    }
}