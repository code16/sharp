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
     *       "start" => 20180101,
     *       "end" => 20181231,
     *    ]
     *
     */
    public function defaultValue();
}