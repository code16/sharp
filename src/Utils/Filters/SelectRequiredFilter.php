<?php

namespace Code16\Sharp\Utils\Filters;

abstract class SelectRequiredFilter extends SelectFilter
{
    abstract public function defaultValue(): mixed;
}
