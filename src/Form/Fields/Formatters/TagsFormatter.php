<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class TagsFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return array
     */
    function toFront(SharpFormField $field, $value)
    {
        return collect((array)$value)
            ->map(function($item) use($field) {

                if(is_object($item) || is_array($item)) {
                    return [
                        "id" => ((array)$item)[$field->idAttribute()],
                    ];
                }

                return ["id" => $item];

            })
            ->all();
    }

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return array
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $options = collect($field->options())->keyBy("id")->all();
        $collection = collect($value)->filter(function($item) use($field, $options) {
            // Strip values that aren't in configured options
            return is_null($item["id"]) || isset($options[$item["id"]]);
        });

        if(! $field->creatable()) {
            // Field isn't creatable, let's just strip all null ids
            return $collection->filter(function($item) {
                return !is_null($item["id"]);
            })->map(function($item) use($field) {
                return [
                    $field->idAttribute() => $item["id"]
                ];
            })->all();
        }

        return $collection->map(function($item) use($field) {
            if(is_null($item["id"])) {
                return [
                    $field->idAttribute() => null,
                    $field->createAttribute() => $item["label"]
                ];
            }

            return $item;

        })->values()->all();
    }
}