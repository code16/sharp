<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\GraphWidgetDisplay;
use Code16\Sharp\Enums\WidgetType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

/**
 * @internal
 */
final class GraphWidgetData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType([
        'key' => 'string',
        'datasets' => 'Array<{ label:string, data:number[], color:string }>',
        'labels' => 'string[]',
    ])]
    public array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.WidgetType::Graph->value.'"')]
        public WidgetType $type,
        public ?string $title,
        public GraphWidgetDisplay $display,
        public bool $showLegend,
        public bool $minimal,
        public ?int $ratioX = null,
        public ?int $ratioY = null,
        public ?int $height = null,
        public bool $dateLabels = false,
        #[TypeScriptType([
            'curved' => 'boolean',
            'horizontal' => 'boolean',
        ])]
        public ?array $options = null,
    ) {}

    public static function from(array $widget): self
    {
        $widget = [
            ...$widget,
            'display' => GraphWidgetDisplay::from($widget['display']),
        ];

        return new self(...$widget);
    }
}
