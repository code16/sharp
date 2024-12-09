<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class EntityListMultiformData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        /** @var array<int|string> */
        public array $instances,
    ) {}
}
