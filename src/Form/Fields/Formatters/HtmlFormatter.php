<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;

class HtmlFormatter extends AbstractSimpleFormatter
{
    /**
     * @param SharpFormHtmlField $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $value ? $field->render($value) : null;
    }
    
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return null;
    }
}
