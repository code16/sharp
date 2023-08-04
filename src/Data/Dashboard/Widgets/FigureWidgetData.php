<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\GraphWidgetDisplay;
use Code16\Sharp\Enums\WidgetType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;


final class FigureWidgetData extends Data
{
    #[Optional]
    #[TypeScriptType([
        'key' => 'string',
        'data' => [
            'figure' => 'string',
            'unit' => 'string',
            'evolution' => [
                'increase' => 'boolean',
                'value' => 'string',
            ],
        ],
    ])]
    public array $value;

    public function __construct(
        public string $key,
        public WidgetType $type,
        public ?string $title,
        public ?string $link,
    ) {
    }

    public static function from(array $widget): self
    {
        return new self(...$widget);
    }
}
