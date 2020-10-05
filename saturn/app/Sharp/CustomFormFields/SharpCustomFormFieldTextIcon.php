<?php

namespace App\Sharp\CustomFormFields;

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;

class SharpCustomFormFieldTextIcon extends SharpFormField
{
    const FIELD_TYPE = "custom-textIcon";

    /** @var string */
    protected $icon;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter);
    }

    /**
     * @param string $iconName
     * @return SharpCustomFormFieldTextIcon
     */
    public function setIcon(string $iconName)
    {
        $this->icon = $iconName;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "icon" => "required",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "icon" => $this->icon,
        ]);
    }
}