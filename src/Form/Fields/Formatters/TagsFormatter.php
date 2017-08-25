<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

class TagsFormatter implements SharpFieldFormatter
{

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return collect($value)
            ->map(function($item) use($field) {
                return [
                    "id" => $item[$field->idAttribute()],
                    "label" => is_callable($field->labelAttribute())
                        ? call_user_func($field->labelAttribute(), $item)
                        : $item[$field->labelAttribute()]
                ];
            })->all();
    }

    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if(! $field->creatable()) {
            // Field isn't creatable, let's just strip all null ids
            return collect($value)->filter(function($item) {
                return !is_null($item["id"]);
            })->all();
        }

        return collect($value)->map(function($item) use($field) {
            if(is_null($item["id"])) {
                return [
                    $field->idAttribute() => $item["id"],
                    $field->createAttribute() => $item["label"]
                ];
            }

            return $item;

        })->values()->all();
    }
}