<?php

namespace Code16\Sharp\Utils\Filters;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait HasFiltersInQuery
{
    protected array $filters;

    public function filterFor(string $filterFullClassNameOrKey): mixed
    {
        if (class_exists($filterFullClassNameOrKey)) {
            $key = tap(
                app($filterFullClassNameOrKey),
                function (Filter $filter) {
                    $filter->buildFilterConfig();
                }
            )
                ->getKey();
        } else {
            $key = $filterFullClassNameOrKey;
        }

        if (!isset($this->filters[$key])) {
            return null;
        }

        if (Str::contains($this->filters[$key], '..')) {
            list($start, $end) = explode('..', $this->filters[$key]);

            return [
                'start' => Carbon::createFromFormat('Ymd', $start)->startOfDay(),
                'end'   => Carbon::createFromFormat('Ymd', $end)->endOfDay(),
            ];
        }

        if (Str::contains($this->filters[$key], ',')) {
            return explode(',', $this->filters[$key]);
        }

        return $this->filters[$key];
    }

    public function setDefaultFilters(array $filters): self
    {
        collect($filters)
            ->each(function ($value, $filter) {
                $this->setFilterValue($filter, $value);
            });

        return $this;
    }

    protected function fillFilterWithRequest(array $query = null): void
    {
        collect($query)
            ->filter(function ($value, $name) {
                return Str::startsWith($name, 'filter_');
            })
            ->each(function ($value, $name) {
                $this->setFilterValue(Str::after($name, 'filter_'), $value);
            });
    }

    protected function setFilterValue(string $filter, array|string|null $value): void
    {
        if (is_array($value)) {
            // Force all filter values to be string, to be consistent with all use cases
            // (filter in EntityList or in Command)
            if (empty($value)) {
                $value = null;
            } elseif (($value['start'] ?? null) instanceof Carbon) {
                // RangeFilter case
                $value = collect($value)
                    ->map->format('Ymd')
                    ->implode('..');
            } else {
                // Multiple filter case
                $value = implode(',', $value);
            }
        }

        $this->filters[$filter] = $value;

        event("filter-{$filter}-was-set", [$value, $this]);
    }
}
