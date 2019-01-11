<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Illuminate\Support\Collection;

trait SharpFormFieldWithOptions
{

    /**
     * @param array|Collection $options
     * @return array
     */
    protected static function formatOptions($options)
    {
        if(! sizeof($options)) {
            return [];
        }

        $options = collect($options);

        if((is_array($options->first()) || is_object($options->first()))
            && isset(((array)$options->first())["id"])) {
            // We assume that we already have ["id", "label"] in this case
            return $options->all();
        }

        // Simple [key => value] array case
        return $options->map(function($label, $id) {
            return [
                "id" => $id, "label" => $label
            ];
        })->values()->all();
    }
}