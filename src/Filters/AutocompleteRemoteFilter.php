<?php

namespace Code16\Sharp\Filters;

abstract class AutocompleteRemoteFilter extends Filter
{
    use AutocompleteRemoteFilterTrait;
    use AutocompleteRemoteSingleFilterTrait;

    public function defaultValue(): mixed
    {
        return null;
    }
}
