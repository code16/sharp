<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

class NumberFormatter
{

    /**
     * @param $value
     * @return mixed
     */
    public function format($value)
    {
        return (int)$value;
    }
}