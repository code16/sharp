<?php

namespace Code16\Sharp\Filters;

abstract class SelectRequiredFilter extends SelectFilter
{
    abstract public function defaultValue(): mixed;
}
