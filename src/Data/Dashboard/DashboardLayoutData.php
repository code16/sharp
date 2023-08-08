<?php

namespace Code16\Sharp\Data\Dashboard;


use Code16\Sharp\Data\Dashboard\Widgets\WidgetData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;

final class DashboardLayoutData extends Data
{
    public function __construct(
        /** @var DataCollection<DashboardLayoutSectionData> */
        public DataCollection $sections,
    ) {
    }

    public static function from(array $layout): self
    {
        $layout = [
            ...$layout,
            'sections' => DashboardLayoutSectionData::collection($layout['sections']),
        ];

        return new self(...$layout);
    }
}
