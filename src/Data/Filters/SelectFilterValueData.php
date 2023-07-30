<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;

class SelectFilterValueData extends Data
{
    public function __construct(
        public int|string $id,
        public string $label
    ) {
    }
}
