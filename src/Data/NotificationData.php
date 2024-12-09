<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Enums\NotificationLevel;

/**
 * @internal
 */
final class NotificationData extends Data
{
    public function __construct(
        public string $title,
        public NotificationLevel $level,
        public ?string $message,
        public bool $autoHide,
    ) {}
}
