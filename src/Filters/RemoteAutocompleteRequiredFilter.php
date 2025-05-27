<?php

namespace Code16\Sharp\Filters;

abstract class RemoteAutocompleteRequiredFilter extends RemoteAutocompleteFilter
{
    abstract public function defaultValue(): mixed;
}
