<?php

namespace Code16\Sharp\Utils\Filters;

abstract class RemoteAutocompleteRequiredFilter extends RemoteAutocompleteFilter
{
    abstract public function defaultValue(): mixed;
}
