<?php

namespace Code16\Sharp\Filters;

abstract class SelectFilter extends Filter
{
    use SelectFilterTrait;

    public function defaultValue(): mixed
    {
        return null;
    }
}
