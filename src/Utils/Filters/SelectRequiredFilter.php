<?php

namespace Code16\Sharp\Utils\Filters;

abstract class SelectRequiredFilter extends SelectFilter
{
    public abstract function defaultValue(): mixed;
}