<?php

namespace Code16\Sharp\Utils\Filters;

interface SelectFilter extends Filter
{
    /**
     * @return array
     */
    public function values();
}

