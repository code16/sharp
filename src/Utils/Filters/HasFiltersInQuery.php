<?php

namespace Code16\Sharp\Utils\Filters;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait HasFiltersInQuery
{
    protected array $filters;

    public function filterFor(string $filterName): mixed
    {
        if(isset($this->filters["/forced/$filterName"])) {
            return $this->filterFor("/forced/$filterName");
        }

        if(!isset($this->filters[$filterName])) {
            return null;
        }

        if(Str::contains($this->filters[$filterName], "..")) {
            list($start, $end) = explode("..", $this->filters[$filterName]);

            return [
                "start" => Carbon::createFromFormat('Ymd', $start)->setTime(0,0,0,0),
                "end" => Carbon::createFromFormat('Ymd', $end)->setTime(23,59,59,999999),
            ];
        }

        if(Str::contains($this->filters[$filterName], ",")){
            return explode(",", $this->filters[$filterName]);
        }

        return $this->filters[$filterName];
    }

    public function setDefaultFilters(array $filters): self
    {
        collect($filters)
            ->each(function($value, $filter) {
                $this->setFilterValue($filter, $value);
            });

        return $this;
    }

    protected function fillFilterWithRequest(array $query = null): void
    {
        collect($query)
            ->filter(function($value, $name) {
                return Str::startsWith($name, "filter_");
            })
            ->each(function($value, $name) {
                $this->setFilterValue(Str::after($name, "filter_"), $value);
            });
    }

    public function forceFilterValue(string $filter, string $value): void
    {
        $this->filters["/forced/$filter"] = $value;
    }

    protected function setFilterValue(string $filter, array|string|null $value): void
    {
        if(is_array($value)) {
            // Force all filter values to be string, to be consistent with all use cases
            // (filter in EntityList or in Command)
            if(empty($value)) {
                $value = null;

            } elseif(isset($value["start"]) && $value["start"] instanceof Carbon) {
                // RangeFilter case
                $value = collect($value)->map->format("Ymd")->implode("..");

            } else {
                // Multiple filter case
                $value = implode(',', $value);
            }
        }

        $this->filters[$filter] = $value;

        event("filter-{$filter}-was-set", [$value, $this]);
    }
}