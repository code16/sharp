<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\GraphWidgetDisplay;
use Code16\Sharp\Enums\WidgetType;

final class WidgetData extends Data
{
    public function __construct(
        public string $key,
        public WidgetType $type,
        public ?string $title,
        public ?string $link,
    ) {
    }

    public static function from(array $widget)
    {
        $widget['type'] = WidgetType::from($widget['type']);

        ray($widget);

        return match ($widget['type']) {
            WidgetType::Graph => GraphWidgetData::from($widget),
            WidgetType::OrderedList => OrderedListWidgetData::from($widget),
            WidgetType::Panel => PanelWidgetData::from($widget),
            WidgetType::Figure => FigureWidgetData::from($widget),
        };
    }
}
