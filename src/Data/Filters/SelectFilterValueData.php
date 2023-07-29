<?php

namespace Code16\Sharp\Data\Filters;

class SelectFilterValueData
{
    public function __construct(
        public int|string $id,
        public string $label
    ) {
    }
}
