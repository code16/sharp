<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class EntityStateValueData extends Data
{
    public function __construct(
        public int|string $value,
        public string $label,
        public string $color,
    ) {}
}
