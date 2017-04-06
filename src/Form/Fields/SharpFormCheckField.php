<?php

namespace Code16\Sharp\Form\Fields;


class SharpFormCheckField extends SharpFormField
{

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
        $instance = new static($key, 'check');
        $instance->text = $text;

        return $instance;
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
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "text" => $this->text
        ]);
    }
}