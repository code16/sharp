<?php

namespace Code16\Sharp\Utils\Filters\Concerns;

use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\Utils\Filters\CheckFilter;
use Code16\Sharp\Utils\Filters\DateRangeFilter;
use Code16\Sharp\Utils\Filters\DateRangeRequiredFilter;
use Code16\Sharp\Utils\Filters\Filter;
use Code16\Sharp\Utils\Filters\SelectFilter;
use Code16\Sharp\Utils\Filters\SelectMultipleFilter;
use Code16\Sharp\Utils\Filters\SelectRequiredFilter;
use Illuminate\Support\Collection;

trait BuildsFiltersConfigArray
{
    public function getFiltersConfigArray(): ?array
    {
        return $this->getFilterHandlers()
            ->map(function (Collection $filterHandlers) {
                return $filterHandlers
                    ->filter(fn (Filter $handler) => ! $this->isHiddenFilter($handler))
                    ->map(function (Filter $filterHandler) {
                        $filterConfigData = [
                            'key' => $filterHandler->getKey(),
                            'label' => $filterHandler->getLabel(),
                        ];

                        if ($filterHandler instanceof SelectFilter) {
                            $multiple = $filterHandler instanceof SelectMultipleFilter;

                            $filterConfigData += [
                                'type' => 'select',
                                'multiple' => $multiple,
                                'required' => ! $multiple && $filterHandler instanceof SelectRequiredFilter,
                                'values' => $this->formatSelectFilterValues($filterHandler),
                                'master' => $filterHandler->isMaster(),
                                'searchable' => $filterHandler->isSearchable(),
                                'searchKeys' => $filterHandler->getSearchKeys(),
                                'template' => $filterHandler->getTemplate(),
                            ];
                        } elseif ($filterHandler instanceof DateRangeFilter) {
                            $filterConfigData += [
                                'type' => 'daterange',
                                'required' => $filterHandler instanceof DateRangeRequiredFilter,
                                'mondayFirst' => $filterHandler->isMondayFirst(),
                                'displayFormat' => $filterHandler->getDateFormat(),
                                'presets' => collect($filterHandler->getPresets())
                                    ->map(fn ($preset, $key) => ['key' => $key, ...$preset->toArray()])
                                    ->values()
                                    ->toArray(),
                            ];
                        } elseif ($filterHandler instanceof CheckFilter) {
                            $filterConfigData += [
                                'type' => 'check',
                            ];
                        }

                        return $filterConfigData;
                    });
            })
            ->filter(fn (Collection $filters) => count($filters) > 0)
            ->toArray()
            ?: null;
    }

    protected function isHiddenFilter(Filter $handler): bool
    {
        return $handler instanceof HiddenFilter;
    }
}
