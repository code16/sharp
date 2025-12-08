<?php

namespace Code16\Sharp\Filters;

abstract class DateRangeFilter extends Filter
{
    use DateRangeFilterTrait;

    public function defaultValue(): mixed
    {
        return null;
    }
}
