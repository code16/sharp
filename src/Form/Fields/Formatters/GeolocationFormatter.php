<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class GeolocationFormatter extends SharpFieldFormatter
{
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

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if ($value && is_array($value)) {
            return implode(',', array_map(
                fn ($val) => str_replace(',', '.', $val),
                array_values($value)
            ));
        }

        return null;
    }
}
