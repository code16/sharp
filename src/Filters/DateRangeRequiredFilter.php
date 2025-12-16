<?php

namespace Code16\Sharp\Filters;

abstract class DateRangeRequiredFilter extends Filter
{
    use DateRangeFilterTrait;

    /**
     * @return array Expected format: ["start" => Carbon::yesterday(), "end" => Carbon::today()]
     */
    abstract public function defaultValue(): array;
}
