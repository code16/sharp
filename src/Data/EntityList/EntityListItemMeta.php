<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class EntityListItemMeta extends Data
{
    public function __construct(
        public ?string $url,
    ) {}
}
