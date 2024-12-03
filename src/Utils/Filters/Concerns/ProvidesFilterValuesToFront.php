<?php

namespace Code16\Sharp\Utils\Filters\Concerns;

use Code16\Sharp\Utils\Filters\DateRangeFilter;
use Code16\Sharp\Utils\Filters\Filter;
use Code16\Sharp\Utils\Filters\SelectMultipleFilter;
use Illuminate\Support\Arr;

trait ProvidesFilterValuesToFront
{
    public function getCurrentFilterValuesForFront(?array $query): array
    {
        $defaultValues = $this->getDefaultFilterValues();
        $currentValues = $this->getCurrentFilterValues($query);

        return [
            'default' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) use ($defaultValues) {
                    return [
                        $handler->getKey() => $this->formatFilterValueForFront(
                            $handler,
                            $defaultValues[$handler->getKey()] ?? null
                        ),
                    ];
                })
                ->toArray(),
            'current' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) use ($currentValues) {
                    return [
                        $handler->getKey() => $this->formatFilterValueForFront(
                            $handler,
                            $currentValues[$handler->getKey()] ?? null
                        ),
                    ];
                })
                ->toArray(),
            // provides information of which filters are valuated (different to default) to conditionally display "Reset all" button
            'valuated' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) use ($currentValues, $defaultValues) {
                    $current = $currentValues[$handler->getKey()] ?? null;
                    $default = $defaultValues[$handler->getKey()] ?? null;
                    if ($handler instanceof SelectMultipleFilter) {
                        $current = is_array($current) ? Arr::sort($current) : [];
                        $default = is_array($default) ? Arr::sort($default) : [];
                    }
                    $isValuated = (string) $handler->toQueryParam($current) !== (string) $handler->toQueryParam($default);

                    return [
                        $handler->getKey() => $isValuated,
                    ];
                })
                ->toArray(),
        ];
    }

    protected function formatFilterValueForFront(Filter $handler, mixed $value)
    {
        if ($value && $handler instanceof DateRangeFilter) {
            $value = [
                'start' => $value['start']->format('Y-m-d'),
                'end' => $value['end']->format('Y-m-d'),
                'formatted' => [
                    'start' => $value['start']->isoFormat($handler->getDateFormat()),
                    'end' => $value['end']->isoFormat($handler->getDateFormat()),
                ],
                'preset' => $value['preset'] ?? null,
            ];
        }

        return $value;
    }
}
