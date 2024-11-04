<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AbstractSimpleFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;

class HtmlFormatter extends SharpShowFieldFormatter
{
    /**
     * @param SharpShowHtmlField $field
     */
    public function toFront(SharpShowField $field, $value)
    {
        return $value ? $field->render($value) : null;
    }
}
