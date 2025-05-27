<?php

namespace Code16\Sharp\Filters;

use Illuminate\Support\Arr;

abstract class RemoteAutocompleteMultipleFilter extends RemoteAutocompleteFilter
{
    public function fromQueryParam($value): array
    {
        return $value ? explode(',', $value) : [];
    }

    public function toQueryParam($value): ?string
    {
        return $value ? implode(',', Arr::wrap($value)) : null;
    }
}
