<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;

class HtmlFormatter extends AbstractSimpleFormatter
{
    protected ?string $fieldKey = null;
    protected ?array $formData = null;

    /**
     * @param  SharpFormHtmlField  $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $field->render([
            'fieldKey' => $this->fieldKey,
            'formData' => $this->formData,
            ...is_array($value) ? $value : [],
        ]);
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return null;
    }

    public function setRenderData(string $fieldKey, array $formData): self
    {
        $this->fieldKey = $fieldKey;
        $this->formData = $formData;

        return $this;
    }
}
