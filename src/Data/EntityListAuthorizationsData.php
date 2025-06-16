<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class EntityListAuthorizationsData extends Data
{
    public function __construct(
        public bool $create,
        public bool $reorder = false,
    ) {}

    public static function from(array $authorizations): self
    {
        return new self(...$authorizations);
    }
}
