<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDate;

class SharpFormPeriodField extends SharpFormField
{
    use SharpFormFieldWithDate;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $instance = new static($key, 'period');
        $instance->startDate = date("Y-m-d");

        return $instance;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "minDate" => "date_format:Y-m-d",
            "maxDate" => "date_format:Y-m-d",
            "startDate" => "required|date_format:Y-m-d",
            "displayFormat" => "required",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "startDate" => $this->startDate,
            "minDate" => $this->minDate,
            "maxDate" => $this->maxDate,
            "displayFormat" => $this->displayFormat,
        ]);
    }
}