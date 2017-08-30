<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\SelectFormatter;
use Illuminate\Support\Collection;

class SharpFormSelectField extends SharpFormField
{
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
     * @param string $key
     * @param array $options
     * @return static
     */
    public static function make(string $key, array $options)
    {
        $instance = new static($key, static::FIELD_TYPE, new SelectFormatter);
        $instance->options = static::formatOptions($options);

        return $instance;
    }

    /**
     * @param array|Collection $options
     * @return array
     */
    private static function formatOptions($options)
    {
        if(! sizeof($options)) {
            return [];
        }

        $options = collect($options);

        if(is_array($options->first())) {
            // We assume that we already have ["id", "label"] in this case
            return $options->all();
        }

        // Simple [key => value] array case
        return $options->map(function($label, $id) {
            return [
                "id" => $id, "label" => $label
            ];
        })->values()->all();
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
     * @param bool $inline
     * @return $this
     */
    public function setInline(bool $inline = true)
    {
        $this->inline = $inline;

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
     * @return bool
     */
    public function idAttribute()
    {
        return $this->idAttribute;
    }

    /**
     * @param string $idAttribute
     * @return $this
     */
    public function setIdAttribute(string $idAttribute)
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
            "inline" => "boolean",
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
            "inline" => $this->inline,
            "maxSelected" => $this->maxSelected,
        ]);
    }
}