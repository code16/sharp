<?php

namespace Code16\Sharp\Data\EntityList;


use Code16\Sharp\Data\Data;

final class EntityListFieldLayoutData extends Data
{
    public function __construct(
        public string $key,
        public string $size,
        public bool $hideOnXs,
        public string $sizeXs,
    ) {
    }
}
