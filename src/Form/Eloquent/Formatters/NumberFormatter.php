<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

class NumberFormatter
{

    /**
     * @param $value
     * @param array $options
     * @return mixed
     */
    public function format($value, array $options = [])
    {
        return (int)$value;
    }
}