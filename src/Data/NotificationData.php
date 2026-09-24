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
        public ?NotificationLevel $level,
        public ?string $message,
        public bool $autoHide,
    ) {}

    public static function from(array $notification): self
    {
        return new self(
            title: $notification['title'],
            level: NotificationLevel::tryFrom($notification['level']),
            message: $notification['message'] ?? null,
            autoHide: $notification['autoHide'] ?? true,
        );
    }
}
