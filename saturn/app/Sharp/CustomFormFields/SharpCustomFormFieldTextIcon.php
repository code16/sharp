<?php

namespace App\Sharp\CustomFormFields;

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;

class SharpCustomFormFieldTextIcon extends SharpFormField
{
    const FIELD_TYPE = "custom-textIcon";

    protected string $icon;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter);
    }

    public function setIcon(string $iconName): self
    {
        $this->icon = $iconName;

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            "icon" => "required",
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            "icon" => $this->icon,
        ]);
    }
}