<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\GraphWidgetDisplay;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    AreaGraphWidgetData::class
    .'|'.BarGraphWidgetData::class
    .'|'.LineGraphWidgetData::class
    .'|'.PieGraphWidgetData::class
)]
/**
 * @internal
 */
final class GraphWidgetData extends Data
{
    public function __construct() {}

    public static function from(array $widget): Data
    {
        $widget = [
            ...$widget,
            'display' => GraphWidgetDisplay::from($widget['display']),
        ];

        return match ($widget['display']) {
            GraphWidgetDisplay::Area => new AreaGraphWidgetData(...$widget),
            GraphWidgetDisplay::Bar => new BarGraphWidgetData(...$widget),
            GraphWidgetDisplay::Line => new LineGraphWidgetData(...$widget),
            GraphWidgetDisplay::Pie => new PieGraphWidgetData(...$widget),
        };
    }
}
