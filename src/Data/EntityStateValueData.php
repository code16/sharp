<?php

namespace Code16\Sharp\Data;


final class EntityStateValueData extends Data
{
    public function __construct(
        public int|string $value,
        public string $label,
        public string $color,
    ) {
    }
}
