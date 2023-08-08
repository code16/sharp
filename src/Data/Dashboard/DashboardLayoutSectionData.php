<?php

namespace Code16\Sharp\Data\Dashboard;


use Code16\Sharp\Data\Dashboard\Widgets\WidgetData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;

final class DashboardLayoutSectionData extends Data
{
    public function __construct(
        public ?string $key,
        public string $title,
        /** @var array<DataCollection<DashboardLayoutWidgetData>> */
        public array $rows,
    ) {
    }

    public static function from(array $section): self
    {
        $section = [
            ...$section,
            'rows' => collect($section['rows'] ?? [])
                ->map(function(array $row) {
                    return collect($row)->map(function(array $widget) {
                        return DashboardLayoutWidgetData::from($widget);
                    });
                })
                ->all(),
        ];

        return new self(...$section);
    }
}
