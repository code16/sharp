<?php

namespace Code16\Sharp\Filters;

abstract class AutocompleteRemoteRequiredFilter extends AutocompleteRemoteFilter
{
    abstract public function defaultValue(): mixed;
}
