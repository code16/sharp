<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Illuminate\Support\Collection;

trait SharpFormFieldWithOptions
{

    /**
     * @param array|Collection $options
     * @param string $idAttribute
     * @return array
     */
    protected static function formatOptions($options, $idAttribute = "id")
    {
        if(! sizeof($options)) {
            return [];
        }

        $options = collect($options);

        if((is_array($options->first()) || is_object($options->first()))
            && isset(((array)$options->first())[$idAttribute])) {
            // We assume that we already have ["id", "label"] in this case
            return $options->all();
        }

        // Simple [key => value] array case
        return $options
            ->map(function($label, $id) {
                return compact('id', 'label');
            })
            ->values()
            ->all();
    }

    /**
     * @param array|Collection $options
     * @param int $depth
     * @return array
     */
    protected static function formatDynamicOptions(&$options, int $depth)
    {
        if(! sizeof($options)) {
            return [];
        }

        return collect($options)
            ->map(function($values) use($depth) {
                if($depth > 1) {
                    return self::formatDynamicOptions($values, $depth-1);
                }

                return collect($values)
                    ->map(function($label, $id) {
                        return compact('id', 'label');
                    })
                    ->values()
                    ->all();
            })
            ->all();
    }
}