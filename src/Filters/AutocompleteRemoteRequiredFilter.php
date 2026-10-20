<?php

namespace Code16\Sharp\Filters;

abstract class AutocompleteRemoteRequiredFilter extends Filter
{
    use AutocompleteRemoteFilterTrait;
    use AutocompleteRemoteSingleFilterTrait;

    abstract public function defaultValue(): mixed;
}
