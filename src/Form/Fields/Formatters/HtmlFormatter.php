<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class HtmlFormatter extends SharpFieldFormatter
{
    /**
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    /**
     * @return null
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return null;
    }
}
