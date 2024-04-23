<?php

namespace Code16\Sharp\Utils\Filters\Concerns;

use Code16\Sharp\Utils\Filters\DateRangeFilter;
use Code16\Sharp\Utils\Filters\Filter;

trait ProvidesFilterValuesToFront
{
    public function getCurrentFilterValuesToFront(): array
    {
        $defaultValues = $this->getDefaultFilterValues();
        $currentValues = $this->getCurrentFilterValues();
        
        return [
            'default' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) use ($defaultValues) {
                    return [
                        $handler->getKey() => $this->formatFilterValueToFront(
                            $handler,
                            $defaultValues[$handler->getKey()] ?? null
                        )
                    ];
                })
                ->toArray(),
            'current' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) use ($currentValues) {
                    return [
                        $handler->getKey() => $this->formatFilterValueToFront(
                            $handler,
                            $currentValues[$handler->getKey()] ?? null
                        )
                    ];
                })
                ->toArray(),
        ];
    }
    
    protected function formatFilterValueToFront(Filter $handler, mixed $value)
    {
        if ($value && $handler instanceof DateRangeFilter) {
            $value = [
                'start' => $value['start']->format('Y-m-d'),
                'end' => $value['end']->format('Y-m-d'),
            ];
        }
        return $value;
    }
}
