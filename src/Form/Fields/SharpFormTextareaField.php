<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\TextareaFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithMaxLength;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithPlaceholder;

class SharpFormTextareaField extends SharpFormField
{
    use SharpFormFieldWithPlaceholder, SharpFormFieldWithMaxLength, SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "textarea";

    /**
     * @var int
     */
    protected $rows;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new TextareaFormatter);
    }

    /**
     * @param int $rows
     * @return $this
     */
    public function setRowCount(int $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "rows" => "integer|min:1",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "rows" => $this->rows,
            "placeholder" => $this->placeholder,
            "maxLength" => $this->maxLength,
            "localized" => $this->localized
        ]);
    }
}