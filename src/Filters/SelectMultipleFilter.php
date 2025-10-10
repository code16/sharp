<?php

namespace Code16\Sharp\Filters;

use Illuminate\Support\Arr;

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
        $values = Arr::wrap($value);

        return count($values)
            ? implode(',', $values)
            : null;
    }
}
