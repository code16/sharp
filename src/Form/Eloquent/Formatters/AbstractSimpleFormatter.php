<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

abstract class AbstractSimpleFormatter
{

    /**
     * @param $value
     * @return mixed
     */
    public function format($value)
    {
        return $value;
    }
}