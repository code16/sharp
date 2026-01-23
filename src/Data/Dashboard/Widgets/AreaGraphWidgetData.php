<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;

use Code16\Sharp\Dashboard\Widgets\Graph\NumberFormatOptions;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\GraphWidgetDisplay;
use Code16\Sharp\Enums\WidgetType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class AreaGraphWidgetData extends Data
{
    #[Optional]
    public GraphWidgetValueData $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.WidgetType::Graph->value.'"')]
        public WidgetType $type,
        #[LiteralTypeScriptType('"'.GraphWidgetDisplay::Area->value.'"')]
        public GraphWidgetDisplay $display,
        public ?string $title,
        public bool $showLegend,
        public bool $minimal,
        public ?int $ratioX = null,
        public ?int $ratioY = null,
        public ?int $height = null,
        public ?NumberFormatOptions $formatOptions = null,
        public bool $displayHorizontalAxisAsTimeline = false,
        public bool $enableHorizontalAxisLabelSampling = false,
        public bool $curved = false,
        public bool $gradient = false,
        public bool $stacked = false,
        public bool $showStackTotal = false,
        public ?string $stackTotalLabel = null,
        public ?float $opacity = null,
    ) {}

    public static function from(array $widget): self
    {
        return new self(...$widget);
    }
}
