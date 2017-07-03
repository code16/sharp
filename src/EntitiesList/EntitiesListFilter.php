<?php

namespace Code16\Sharp\EntitiesList;

abstract class EntitiesListFilter
{

    /**
     * @var string|array
     */
    protected $currentValue;

    /**
     * @var bool
     */
    protected $multiple = false;

    /**
     * @return array|string
     */
    public function currentValue()
    {
        return $this->currentValue;
    }

    public function setCurrentValue($value)
    {
        $this->currentValue = $value;
    }

    public function multiple()
    {
        return $this->multiple;
    }

    abstract public function values();
}