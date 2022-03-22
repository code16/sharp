<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class WysiwygFormatter extends SharpFieldFormatter
{
    /**
     * @param  SharpFormField  $field
     * @param $value
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        return [
            'text' => $value,
        ];
    }

    /**
     * @param  SharpFormField  $field
     * @param  string  $attribute
     * @param $value
     * @return mixed
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $value['text'] ?? '';
    }
}
