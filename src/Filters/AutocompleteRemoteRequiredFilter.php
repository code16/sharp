<?php

namespace Code16\Sharp\Filters;

abstract class AutocompleteRemoteRequiredFilter extends Filter
{
    use AutocompleteRemoteFilterTrait;

    abstract public function defaultValue(): mixed;
}
