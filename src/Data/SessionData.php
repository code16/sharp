<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Enums\SessionStatusLevel;

/**
 * @internal
 */
final class SessionData extends Data
{
    public function __construct(
        public string $_token,
        public ?string $status = null,
        public ?string $status_title = null,
        public ?SessionStatusLevel $status_level = null,
    ) {}

    public static function from(array $session): self
    {
        return new self(...$session);
    }
}
