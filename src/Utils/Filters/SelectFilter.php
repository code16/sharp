<?php

namespace Code16\Sharp\Utils\Filters;

interface SelectFilter extends Filter
{
    /**
     * @return array
     */
    public function values();
}

interface SelectMultipleFilter extends SelectFilter
{
}

interface SelectRequiredFilter extends SelectFilter
{
    /**
     * @return string|int
     */
    public function defaultValue();
}