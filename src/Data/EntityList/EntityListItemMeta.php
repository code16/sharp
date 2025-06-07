<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\InstanceAuthorizationsData;

/**
 * @internal
 */
final class EntityListItemMeta extends Data
{
    public function __construct(
        public ?string $url,
        public InstanceAuthorizationsData $authorizations,
    ) {}
}
