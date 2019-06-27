<?php

namespace Code16\Sharp\Utils\Filters;

interface DateRangeFilter extends Filter
{
}

interface DateRangeRequiredFilter extends DateRangeFilter
{
    /**
     * @return array
     *
     * awaited format:
     *
     *    [
     *       "start" => Carbon::yesterday(),
     *       "end" => Carbon::today(),
     *    ]
     *
     */
    public function defaultValue();
}