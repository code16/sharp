<?php

namespace Code16\Sharp\Utils\Filters;

abstract class SelectMultipleFilter extends SelectFilter
{
    public function fromQueryParam($value): array
    {
        return $value ? explode(',', $value) : [];
    }
    
    public function toQueryParam($value): ?string
    {
        return $value ? implode(',', $value) : null;
    }
}
