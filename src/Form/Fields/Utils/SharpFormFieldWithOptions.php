<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;
use Code16\Sharp\Utils\Transformers\ArrayConverter;
use Illuminate\Support\Collection;

trait SharpFormFieldWithOptions
{
    protected static function formatOptions(array|Collection $options, string $idAttribute = 'id', ?Closure $format = null): array
    {
        if (! count($options)) {
            return [];
        }

        $options = collect($options);
        $firstOption = ArrayConverter::modelToArray($options->first());
        $format ??= fn ($option) => $option;

        if (is_array($firstOption) && isset($firstOption[$idAttribute])) {
            // We assume that we already have ["id", "label"] in this case
            return $options->map($format)->values()->all();
        }

        // Simple [key => value] array case
        return $options
            ->map(fn ($label, $id) => compact('id', 'label'))
            ->map($format)
            ->values()
            ->all();
    }

    protected static function formatDynamicOptions(array|Collection &$options, int $depth, string $idAttribute = 'id', ?Closure $format = null): array
    {
        if (! count($options)) {
            return [];
        }

        return collect($options)
            ->map(fn ($values) => $depth > 1
                ? self::formatDynamicOptions($values, $depth - 1, $idAttribute, $format)
                : self::formatOptions($values, 'id', $format)
            )
            ->all();
    }
}
