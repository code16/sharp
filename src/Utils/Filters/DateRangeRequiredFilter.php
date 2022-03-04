<?php

namespace Code16\Sharp\Utils\Filters;

interface DateRangeRequiredFilter extends DateRangeFilter
{
    /**
     * @return array
     *               Expected format: ["start" => Carbon::yesterday(), "end" => Carbon::today()]
     */
    public function defaultValue(): array;
}
