<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\CheckFormatter;

class SharpFormCheckField extends SharpFormField
{
    const FIELD_TYPE = 'check';

    protected string $text;

    public static function make(string $key, string $text): self
    {
        $instance = new static($key, static::FIELD_TYPE, new CheckFormatter);
        $instance->text = $text;

        return $instance;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'text' => 'required',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'text' => $this->text,
        ]);
    }
}
