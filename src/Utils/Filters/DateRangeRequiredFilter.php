<?php

namespace Code16\Sharp\Utils\Filters;

abstract class DateRangeRequiredFilter extends DateRangeFilter
{
    /**
     * @return array
     *               Expected format: ["start" => Carbon::yesterday(), "end" => Carbon::today()]
     */
    abstract public function defaultValue(): array;
}
