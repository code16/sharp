<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\GraphWidgetDisplay;
use Code16\Sharp\Enums\WidgetType;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    FigureWidgetData::class
    .'|'.GraphWidgetData::class
    .'|'.OrderedListWidgetData::class
    .'|'.PanelWidgetData::class
)]
final class WidgetData extends Data
{
    public function __construct() {
    }

    public static function from(array $widget)
    {
        $widget['type'] = WidgetType::from($widget['type']);

        return match ($widget['type']) {
            WidgetType::Figure => FigureWidgetData::from($widget),
            WidgetType::Graph => GraphWidgetData::from($widget),
            WidgetType::OrderedList => OrderedListWidgetData::from($widget),
            WidgetType::Panel => PanelWidgetData::from($widget),
        };
    }
}
