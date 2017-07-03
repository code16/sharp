<?php

namespace Code16\Sharp\EntitiesList;

abstract class EntitiesListFilter
{

    /**
     * @var string|array
     */
    protected $currentValue;

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

    abstract public function values();
}