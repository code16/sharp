<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

class CheckFormatter
{

    /**
     * @param $value
     * @param array $options
     * @return mixed
     */
    public function format($value, array $options = [])
    {
        if(is_string($value) && strlen($value)) {
            return !in_array($value, [
                "false", "0", "off"
            ]);
        }

        return !! $value;
    }
}