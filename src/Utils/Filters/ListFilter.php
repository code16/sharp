<?php

namespace Code16\Sharp\Utils\Filters;

interface ListFilter
{
    /**
     * @return array
     */
    public function values();
}

interface ListMultipleFilter extends ListFilter
{
}

interface ListRequiredFilter extends ListFilter
{
    /**
     * @return string|int
     */
    public function defaultValue();
}