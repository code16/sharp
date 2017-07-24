<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Illuminate\Support\Collection;

class SharpGraphWidgetDataSet
{

    /**
     * @var array
     */
    protected $values;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $color;

    /**
     * @param array|Collection $values
     */
    protected function __construct($values)
    {
        $this->values = $values instanceof Collection
            ? $values->toArray()
            : $values;
    }

    /**
     * @param array|Collection $values
     * @return static
     */
    public static function make($values)
    {
        $instance = new static($values);

        return $instance;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function setColor(string $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "values" => array_values($this->values),
            "labels" => array_keys($this->values)
        ]
            + ($this->label ? ["label" => $this->label] : [])
            + ($this->color ? ["color" => $this->color] : []);
    }
}