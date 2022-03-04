<?php

namespace Code16\Sharp\Utils\Filters;

interface SelectRequiredFilter extends SelectFilter
{
    /**
     * @return string|int
     */
    public function defaultValue();
}
