<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Utils\Fields\HandleFormFields;

class HtmlFormatter extends AbstractSimpleFormatter
{
    /**
     * HTML fields formatting is done separately.
     *
     * @see HandleFormFields::formatHtmlFields()
     *
     * @param  SharpFormHtmlField  $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return null;
    }
}
