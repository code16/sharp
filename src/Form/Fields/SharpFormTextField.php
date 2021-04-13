<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithMaxLength;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormTextField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder, SharpFormFieldWithMaxLength, SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "text";

    protected string $inputType = "text";

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter);
    }

    public function setInputTypeText(): self
    {
        $this->inputType = "text";

        return $this;
    }

    public function setInputTypePassword(): self
    {
        $this->inputType = "password";

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            "inputType" => "required|in:text,password",
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            "inputType" => $this->inputType,
            "placeholder" => $this->placeholder,
            "maxLength" => $this->maxLength,
            "localized" => $this->localized
        ]);
    }
}