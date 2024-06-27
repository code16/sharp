<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;

class CustomField extends SharpFormField
{
    const FIELD_TYPE = 'custom';
    
    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter);
    }
    
    public function toArray(): array
    {
        return parent::buildArray([
            'custom' => 'lol'
        ]);
    }
}
