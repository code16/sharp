<?php

namespace Code16\Sharp\Filters;

use Code16\Sharp\Enums\FilterType;

class CheckFilter extends Filter
{
    public function fromQueryParam($value): mixed
    {
        return (bool) $value;
    }

    public function toQueryParam($value): mixed
    {
        return $value;
    }

    public function defaultValue(): bool
    {
        return false;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'type' => FilterType::Check->value,
        ]);
    }
}
