<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class HtmlFormatter extends SharpFieldFormatter
{
    /**
     * @param  SharpFormField  $field
     * @param  $value
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    /**
     * @param  SharpFormField  $field
     * @param  string  $attribute
     * @param  $value
     * @return null
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return null;
    }
}
