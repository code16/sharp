<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Fields\SharpFormTagsField;

class TagsFormatter
{
    /**
     * @param array $value
     * @param SharpFormTagsField $field
     * @return mixed
     */
    public function format($value, $field)
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
                    "id" => $item["id"],
                    $field->createAttribute() => $item["label"]
                ];
            }

            return $item;

        })->values()->all();
    }
}