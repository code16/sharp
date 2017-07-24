<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormTextField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder;

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
        return new static($key, static::FIELD_TYPE);
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
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "inputType" => $this->inputType,
            "placeholder" => $this->placeholder,
        ]);
    }
}