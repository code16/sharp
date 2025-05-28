<?php

namespace Code16\Sharp\Filters;

abstract class AutocompleteRemoteMultipleFilter extends AutocompleteRemoteFilter
{
    public function formatRawValue(mixed $value): mixed
    {
        return $value
            ? collect($value)->pluck('id')->all()
            : null;
    }
}
