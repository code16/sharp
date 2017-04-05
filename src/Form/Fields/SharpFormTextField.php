<?php

namespace Code16\Sharp\Form\Fields;

class SharpFormTextField extends SharpFormField
{
    /**
     * @var string
     */
    protected $inputType = "text";

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, 'text');
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
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::makeArray([
            "inputType" => $this->inputType,
            "placeholder" => $this->placeholder,
        ]);
    }
}