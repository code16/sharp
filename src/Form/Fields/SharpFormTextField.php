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

    /**
     * @var string
     */
    protected $inputType = "text";

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter);
    }

    /**
     * @return $this
     */
    public function setInputTypeText()
    {
        $this->inputType = "text";

        return $this;
    }

    /**
     * @return $this
     */
    public function setInputTypePassword()
    {
        $this->inputType = "password";

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "inputType" => "required|in:text,password",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
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