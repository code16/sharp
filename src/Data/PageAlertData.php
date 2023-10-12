<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Enums\PageAlertLevel;

final class PageAlertData extends Data
{
    public function __construct(
        public PageAlertLevel $level,
        public string $text,
    ) {
    }

    public static function from(array $pageAlert): self
    {
        return new self(...$pageAlert);
    }
}
