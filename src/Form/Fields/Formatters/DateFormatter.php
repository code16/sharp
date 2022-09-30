<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Illuminate\Support\Carbon;

class DateFormatter extends SharpFieldFormatter
{
    public function toFront(SharpFormField $field, $value)
    {
        if ($value instanceof \DateTime || is_string($value)) {
            $value = (new Carbon($value))
                ->setTimezone(config('app.timezone'));
        }

        if (! $value instanceof Carbon) {
            return $value;
        }

        return $field->hasDate()
            ? $value->toJSON()
            : $value->format('H:i');
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $format = 'Y-m-d H:i:s';
        if (! $field->hasTime()) {
            $format = 'Y-m-d';
        }
        if (! $field->hasDate()) {
            $format = 'H:i:s';
        }

        return $value
            ? Carbon::parse($value)
                ->setTimezone(config('app.timezone'))
                ->format($format)
            : null;
    }
}
