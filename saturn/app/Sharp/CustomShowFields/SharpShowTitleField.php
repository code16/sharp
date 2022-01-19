<?php

namespace App\Sharp\CustomShowFields;

use Code16\Sharp\Show\Fields\SharpShowField;

class SharpShowTitleField extends SharpShowField
{
    const FIELD_TYPE = 'custom-title';

    protected int $level = 1;

    public static function make(string $key): SharpShowTitleField
    {
        return new static($key, static::FIELD_TYPE);
    }

    public function setTitleLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            'level' => 'required|integer|min:1|max:5',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'level' => $this->level,
        ]);
    }
}
