<?php

namespace Code16\Sharp\Data\Dashboard;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class DashboardLayoutData extends Data
{
    public function __construct(
        /** @var DashboardLayoutSectionData[] */
        public array $sections,
    ) {}

    public static function from(array $layout): self
    {
        $layout = [
            ...$layout,
            'sections' => DashboardLayoutSectionData::collection($layout['sections']),
        ];

        return new self(...$layout);
    }
}
