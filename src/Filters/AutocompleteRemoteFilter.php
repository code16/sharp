<?php

namespace Code16\Sharp\Filters;

abstract class AutocompleteRemoteFilter extends Filter
{
    use AutocompleteRemoteFilterTrait;

    public function defaultValue(): mixed
    {
        return null;
    }
}
