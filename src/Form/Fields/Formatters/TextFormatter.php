<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldDataException;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\Formatters\FormatsSanitizedValue;

class TextFormatter extends AbstractSimpleFormatter
{
    use FormatsSanitizedValue;

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
        return $this->maybeLocalized(
            $field,
            $value,
            fn (string $content) => $this->sanitizeHtmlIfNeeded($field, $content)
        );
    }
}
