<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\SelectFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithOptions;

class SharpFormSelectField extends SharpFormField
{
    use SharpFormFieldWithOptions, SharpFormFieldWithDataLocalization;

    const FIELD_TYPE = "select";

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $multiple = false;

    /**
     * @var bool
     */
    protected $clearable = false;

    /**
     * @var int
     */
    protected $maxSelected = null;

    /**
     * @var string
     */
    protected $display = "list";

    /**
     * @var string
     */
    protected $idAttribute = "id";

    /**
     * @var bool
     */
    protected $inline = false;

    /**
     * @var bool
     */
    protected $showSelectAll = false;

    /**
     * @var array
     */
    protected $dynamicAttributes;

    /**
     * @param string $key
     * @param array $options
     * @return static
     */
    public static function make(string $key, array $options)
    {
        $instance = new static($key, static::FIELD_TYPE, new SelectFormatter);
        $instance->options = $options;

        return $instance;
    }

    public function setMultiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function allowSelectAll(bool $allowSelectAll = true): self
    {
        $this->showSelectAll = $allowSelectAll;
        
        return $this;
    }

    public function setClearable(bool $clearable = true): self
    {
        $this->clearable = $clearable;

        return $this;
    }

    public function setInline(bool $inline = true): self
    {
        $this->inline = $inline;

        return $this;
    }

    public function setDisplayAsList(): self
    {
        $this->display = "list";

        return $this;
    }

    public function setDisplayAsDropdown(): self
    {
        $this->display = "dropdown";

        return $this;
    }

    public function setMaxSelected(int $maxSelected): self
    {
        $this->maxSelected = $maxSelected;

        return $this;
    }

    public function setMaxSelectedUnlimited(): self
    {
        $this->maxSelected = null;

        return $this;
    }

    public function setOptionsLinkedTo(string ...$fieldKeys): self
    {
        $this->dynamicAttributes = [
            [
                "name" => "options",
                "type" => "map",
                "path" => $fieldKeys
            ]
        ];

        return $this;
    }

    public function multiple(): bool
    {
        return $this->multiple;
    }

    public function idAttribute(): string
    {
        return $this->idAttribute;
    }

    public function setIdAttribute(string $idAttribute): self
    {
        $this->idAttribute = $idAttribute;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "options" => "array",
            "multiple" => "boolean",
            "showSelectAll" => "boolean",
            "inline" => "boolean",
            "clearable" => "boolean",
            "display" => "required|in:list,dropdown",
            "maxSelected" => "int"
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray(
            array_merge([
                "options" => $this->dynamicAttributes
                    ? self::formatDynamicOptions($this->options, count($this->dynamicAttributes[0]["path"]))
                    : self::formatOptions($this->options, $this->idAttribute),
                "multiple" => $this->multiple,
                "showSelectAll" => $this->showSelectAll,
                "clearable" => $this->clearable,
                "display" => $this->display,
                "inline" => $this->inline,
                "maxSelected" => $this->maxSelected,
                "localized" => $this->localized
            ],
                $this->dynamicAttributes
                    ? ["dynamicAttributes" => $this->dynamicAttributes]
                    : []
            )
        );
    }
}