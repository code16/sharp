<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Enums\PageAlertLevel;

/**
 * @internal
 */
final class PageAlertData extends Data
{
    public function __construct(
        public PageAlertLevel $level,
        public string $text,
        public ?string $sectionKey = null,
        public ?string $buttonLabel = null,
        public ?string $buttonUrl = null,
    ) {}

    public static function from(array $pageAlert): self
    {
        return new self(...$pageAlert);
    }
}
