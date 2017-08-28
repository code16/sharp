<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\SelectFormatter;

class SharpFormSelectField extends SharpFormField
{
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
     * @param string $key
     * @param array $options
     * @return static
     */
    public static function make(string $key, array $options)
    {
        $instance = new static($key, 'select', new SelectFormatter);
        $instance->options = collect($options)->map(function($label, $id) {
            return [
                "id" => $id, "label" => $label
            ];
        })->values()->all();

        return $instance;
    }

    /**
     * @param bool $multiple
     * @return $this
     */
    public function setMultiple(bool $multiple = true)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @param bool $clearable
     * @return $this
     */
    public function setClearable(bool $clearable = true)
    {
        $this->clearable = $clearable;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDisplayAsList()
    {
        $this->display = "list";

        return $this;
    }

    /**
     * @return $this
     */
    public function setDisplayAsDropdown()
    {
        $this->display = "dropdown";

        return $this;
    }

    /**
     * @param int $maxSelected
     * @return $this
     */
    public function setMaxSelected(int $maxSelected)
    {
        $this->maxSelected = $maxSelected;

        return $this;
    }

    /**
     * @return static
     */
    public function setMaxSelectedUnlimited()
    {
        $this->maxSelected = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function multiple()
    {
        return $this->multiple;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "options" => "required|array",
            "multiple" => "boolean",
            "clearable" => "boolean",
            "display" => "required|in:list,dropdown",
            "maxSelected" => "int"
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "options" => $this->options,
            "multiple" => $this->multiple,
            "clearable" => $this->clearable,
            "display" => $this->display,
            "maxSelected" => $this->maxSelected,
        ]);
    }
}