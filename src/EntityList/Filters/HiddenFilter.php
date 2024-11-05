<?php

namespace Code16\Sharp\EntityList\Filters;

use Code16\Sharp\Utils\Filters\Filter;

class HiddenFilter extends Filter
{
    public static function make(string $key): self
    {
        return tap(new static(), function (Filter $filter) use ($key) {
            $filter->configureKey($key);
        });
    }

    public function fromQueryParam($value): mixed
    {
        return $value;
    }

    public function toQueryParam($value): mixed
    {
        return $value;
    }
}
