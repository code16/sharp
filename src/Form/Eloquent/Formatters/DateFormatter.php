<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

use Carbon\Carbon;

class DateFormatter
{

    /**
     * @param $value
     * @param array $options
     * @return mixed
     */
    public function format($value, array $options = [])
    {
        return Carbon::parse($value);
    }
}