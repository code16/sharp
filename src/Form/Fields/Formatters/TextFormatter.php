<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldDataException;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;

class TextFormatter extends AbstractSimpleFormatter
{
    /**
     * @param  SharpFormTextField  $field
     *
     * @throws SharpFormFieldDataException
     */
    public function toFront(SharpFormField $field, $value)
    {
        $this->guardAgainstInvalidLocalizedValue($field, $value);

        return parent::toFront($field, $value);
    }

    /**
     * @param  SharpFormTextField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $this->maybeLocalized($field, $value);
    }
}
