<?php

namespace Code16\Sharp\Data\Show;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class ShowFieldAuthorizationsData extends Data
{
    public function __construct(
        public bool $view
    ) {}

    public static function from(array $authorizations): self
    {
        return new self(...$authorizations);
    }
}
