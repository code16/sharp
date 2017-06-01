<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

use Carbon\Carbon;

class DateFormatter
{

    /**
     * @param $value
     * @return mixed
     */
    public function format($value)
    {
        return Carbon::parse($value);
    }
}