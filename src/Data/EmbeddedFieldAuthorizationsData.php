<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class EmbeddedFieldAuthorizationsData extends Data
{
    public function __construct(
        public bool $view
    ) {}

    public static function from(array $authorizations): self
    {
        return new self(...$authorizations);
    }
}
