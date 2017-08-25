<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Carbon\Carbon;
use Code16\Sharp\Form\Fields\SharpFormField;

class DateFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        // TODO FIX Timezone
        return Carbon::parse($value, config("app.timezone"))
            ->format("Y-m-d H:i:s");
    }
}