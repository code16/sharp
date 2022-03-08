<?php

namespace App\Sharp\Utils;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Support\Carbon;

class DateTimeCustomTransformer implements SharpAttributeTransformer
{
    public function apply($value, $instance = null, $attribute = null)
    {
        $carbonValue = $instance->$attribute;

        return $carbonValue && $carbonValue instanceof Carbon
            ? $carbonValue->isoFormat('LLLL')
            : null;
    }
}
