<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class GeolocationFormatter extends SharpFieldFormatter
{
    /**
     * @param  SharpFormField  $field
     * @param  $value
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        if ($value && strpos($value, ',')) {
            [$lat, $long] = explode(',', $value);
            $lat = (float) $lat;
            $lng = (float) $long;

            return compact('lat', 'lng');
        }

        return null;
    }

    /**
     * @param  SharpFormField  $field
     * @param  string  $attribute
     * @param  $value
     * @return string
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if ($value && is_array($value)) {
            return implode(',', array_map(function ($val) {
                return str_replace(',', '.', $val);
            }, array_values($value)));
        }

        return null;
    }
}
