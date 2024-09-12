<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Code16\Sharp\Utils\Transformers\ArrayConverter;
use Illuminate\Support\Collection;

trait SharpFormFieldWithOptions
{
    protected static function formatOptions(array|Collection $options, string $idAttribute = 'id'): array
    {
        if (! sizeof($options)) {
            return [];
        }

        $options = collect($options);
        $firstOption = ArrayConverter::modelToArray($options->first());

        if (is_array($firstOption) && isset($firstOption[$idAttribute])) {
            // We assume that we already have ["id", "label"] in this case
            return $options->all();
        }

        // Simple [key => value] array case
        return $options
            ->map(fn ($label, $id) => compact('id', 'label'))
            ->values()
            ->all();
    }

    protected static function formatDynamicOptions(array|Collection &$options, int $depth): array
    {
        if (! sizeof($options)) {
            return [];
        }

        return collect($options)
            ->map(function ($values) use ($depth) {
                if ($depth > 1) {
                    return self::formatDynamicOptions($values, $depth - 1);
                }

                return collect($values)
                    ->map(fn ($label, $id) => compact('id', 'label'))
                    ->values()
                    ->all();
            })
            ->all();
    }
}
