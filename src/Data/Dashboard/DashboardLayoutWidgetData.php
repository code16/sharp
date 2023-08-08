<?php

namespace Code16\Sharp\Data\Dashboard;


use Code16\Sharp\Data\Dashboard\Widgets\WidgetData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;

final class DashboardLayoutWidgetData extends Data
{
    public function __construct(
        public int $size,
        public string $key
    ) {
    }

    public static function from(array $layoutWidget): self
    {
        return new self(...$layoutWidget);
    }
}
