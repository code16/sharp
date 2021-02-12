<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\TextareaFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithMaxLength;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormTextareaField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder, SharpFormFieldWithMaxLength, SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "textarea";

    protected ?int $rows = null;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new TextareaFormatter);
    }

    public function setRowCount(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            "rows" => "integer|min:1",
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            "rows" => $this->rows,
            "placeholder" => $this->placeholder,
            "maxLength" => $this->maxLength,
            "localized" => $this->localized
        ]);
    }
}