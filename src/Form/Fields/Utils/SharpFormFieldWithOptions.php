<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;
use Code16\Sharp\Utils\Transformers\ArrayConverter;
use Illuminate\Support\Collection;

trait SharpFormFieldWithOptions
{
    protected static function formatOptions(array|Collection $options, string $idAttribute = 'id', ?Closure $format = null): array
    {
        if (! sizeof($options)) {
            return [];
        }

        $options = collect($options);
        $firstOption = ArrayConverter::modelToArray($options->first());
        $format ??= fn ($option) => $option;

        if (is_array($firstOption) && isset($firstOption[$idAttribute])) {
            // We assume that we already have ["id", "label"] in this case
            return $options
                ->when($format)->map($format)
                ->all();
        }

        // Simple [key => value] array case
        return $options
            ->map(fn ($label, $id) => compact('id', 'label'))
            ->when($format)->map($format)
            ->all();
    }

    protected static function formatDynamicOptions(array|Collection &$options, int $depth, ?Closure $format = null): array
    {
        if (! sizeof($options)) {
            return [];
        }

        return collect($options)
            ->map(function ($values) use ($depth, $format) {
                if ($depth > 1) {
                    return self::formatDynamicOptions($values, $depth - 1);
                }

                return collect($values)
                    ->map(fn ($label, $id) => compact('id', 'label'))
                    ->when($format)->map($format)
                    ->values()
                    ->all();
            })
            ->all();
    }
}
