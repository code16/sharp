<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

abstract class AbstractSimpleFormatter
{

    /**
     * @param $value
     * @param array $options
     * @return mixed
     */
    public function format($value, array $options = [])
    {
        return $value;
    }
}