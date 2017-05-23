<?php

namespace Code16\Sharp\Form\Fields;

class SharpFormSelectField extends SharpFormField
{
    /**
     * @var array
     */
    protected $values;

    /**
     * @var bool
     */
    protected $multiple = false;

    /**
     * @var bool
     */
    protected $clearable = false;

    /**
     * @var string
     */
    protected $display = "list";

    /**
     * @param string $key
     * @param array $values
     * @return static
     */
    public static function make(string $key, array $values)
    {
        $instance = new static($key, 'select');
        $instance->values = $values;

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
     * @return array
     */
    protected function validationRules()
    {
        return [
            "values" => "required|array",
            "multiple" => "boolean",
            "clearable" => "boolean",
            "display" => "required|in:list,dropdown"
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "values" => $this->values,
            "multiple" => $this->multiple,
            "clearable" => $this->clearable,
            "display" => $this->display
        ]);
    }
}