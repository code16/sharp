<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Transformers\ArrayConverter;

class TagsFormatter extends SharpFieldFormatter
{
    /**
     * @param SharpFormField $field
     * @param $value
     *
     * @return array
     */
    public function toFront(SharpFormField $field, $value)
    {
        return collect((array) $value)
            ->map(function ($item) use ($field) {
                $item = ArrayConverter::modelToArray($item);

                if (is_array($item)) {
                    return [
                        'id' => $item[$field->idAttribute()],
                    ];
                }

                return ['id' => $item];
            })
            ->all();
    }

    /**
     * @param SharpFormField $field
     * @param string         $attribute
     * @param $value
     *
     * @return array
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $options = collect($field->options())->keyBy('id')->all();

        return collect($value)
            ->filter(function ($item) use ($options) {
                // Strip values that aren't in configured options
                return is_null($item['id']) || isset($options[$item['id']]);
            })

            ->when(!$field->creatable(), function ($collection) {
                // Field isn't creatable, let's just strip all null ids
                return $collection->filter(function ($item) {
                    return !is_null($item['id']);
                });
            })

            ->map(function ($item) use ($field) {
                if (is_null($item['id'])) {
                    return array_merge(
                        [
                            $field->idAttribute()     => null,
                            $field->createAttribute() => $item['label'],
                        ],
                        $field->createAdditionalAttributes()
                    );
                }

                return [
                    $field->idAttribute() => $item['id'],
                ];
            })
            ->values()
            ->all();
    }
}
