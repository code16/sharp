<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class GeolocationFormatter extends SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        if($value && strpos($value, ",")) {
            list($lat, $long) = explode(",", $value);
            $lat = trim($lat);
            $long = trim($long);

            return compact('lat', 'long');
        }

        return null;
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return string
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if($value && is_array($value)) {
            return implode(",", $value);
        }

        return null;
    }
}