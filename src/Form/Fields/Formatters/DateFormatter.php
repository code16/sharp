<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Carbon\Carbon;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormField;

class DateFormatter extends SharpFieldFormatter
{
    /**
     * @param SharpFormField $field
     * @param $value
     *
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        if ($value instanceof Carbon || $value instanceof \DateTime) {
            return $value->format($this->getFormat($field));
        }

        return $value;
    }

    /**
     * @param SharpFormField $field
     * @param string         $attribute
     * @param $value
     *
     * @return string
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $value
            ? Carbon::parse($value)
                ->setTimezone(config('app.timezone'))
                ->format($this->getFormat($field))
            : null;
    }

    /**
     * @param SharpFormDateField $field
     *
     * @return string
     */
    protected function getFormat($field)
    {
        if (!$field->hasTime()) {
            return 'Y-m-d';
        }

        if (!$field->hasDate()) {
            return 'H:i:s';
        }

        return 'Y-m-d H:i:s';
    }
}
