<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;

final class FilterData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public FilterType $type,
        public mixed $default,
    ) {
    }
}
