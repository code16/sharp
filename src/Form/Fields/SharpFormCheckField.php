<?php

namespace Code16\Sharp\Form\Fields;


use Code16\Sharp\Form\Fields\Formatters\CheckFormatter;

class SharpFormCheckField extends SharpFormField
{
    const FIELD_TYPE = "check";

    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $key
     * @param string $text
     * @return static
     */
    public static function make(string $key, string $text)
    {
        $instance = new static($key, static::FIELD_TYPE, new CheckFormatter);
        $instance->text = $text;

        return $instance;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "text" => "required",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "text" => $this->text
        ]);
    }
}