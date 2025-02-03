<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class InstanceAuthorizationsData extends Data
{
    public function __construct(
        public bool $view,
        public bool $create,
        public bool $update,
        public bool $delete,
    ) {}

    public static function from(array $authorizations): self
    {
        return new self(...$authorizations);
    }
}
