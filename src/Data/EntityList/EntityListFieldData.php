<?php

namespace Code16\Sharp\Data\EntityList;


use Code16\Sharp\Data\Data;

final class EntityListFieldData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public bool $sortable,
        public bool $html,
    ) {
    }
}
