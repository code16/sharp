<?php

namespace Code16\Sharp\Filters;

abstract class SelectMultipleFilter extends SelectFilter
{
    public function fromQueryParam($value): array
    {
        return $value !== null
            ? explode(',', $value)
            : [];
    }

    public function toQueryParam($value): ?string
    {
        return $value && count($value)
            ? collect($value)->implode(',')
            : null;
    }
}
