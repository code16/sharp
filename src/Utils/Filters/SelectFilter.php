<?php

namespace Code16\Sharp\Utils\Filters;

interface SelectFilter extends Filter
{
    public function values(): array;
}
