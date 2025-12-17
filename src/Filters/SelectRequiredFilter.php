<?php

namespace Code16\Sharp\Filters;

abstract class SelectRequiredFilter extends Filter
{
    use SelectFilterTrait;

    abstract public function defaultValue(): mixed;
}
