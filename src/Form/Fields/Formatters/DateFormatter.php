<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Carbon\Carbon;
use Code16\Sharp\Form\Fields\SharpFormDateField;
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
        if($value instanceof Carbon || $value instanceof \DateTime) {
            return $value->format($this->getFormat($field));
        }

        return $value;
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return string
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        // TODO FIX Timezone
        return Carbon::parse($value, config("app.timezone"))
            ->format($this->getFormat($field));
    }

    /**
     * @param SharpFormDateField $field
     * @return string
     */
    protected function getFormat($field)
    {
        if(!$field->hasTime()) {
            return "Y-m-d";
        }

        if(!$field->hasDate()) {
            return "H:i:s";
        }

        return "Y-m-d H:i:s";
    }
}