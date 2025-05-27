<?php

namespace Code16\Sharp\Utils\Filters;

abstract class CheckFilter extends Filter
{
    public function fromQueryParam($value): mixed
    {
        return (bool) $value;
    }

    public function toQueryParam($value): mixed
    {
        return $value;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'type' => 'check',
        ]);
    }
}
