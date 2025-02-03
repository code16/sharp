<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\WidgetType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

/**
 * @internal
 */
final class FigureWidgetData extends Data
{
    #[Optional]
    #[TypeScriptType([
        'key' => 'string',
        'data' => [
            'figure' => 'string',
            'unit' => 'string | null',
            'evolution' => 'string | null',
        ],
    ])]
    public array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.WidgetType::Figure->value.'"')]
        public WidgetType $type,
        public ?string $title = null,
        public ?string $link = null,
    ) {}

    public static function from(array $widget): self
    {
        return new self(...$widget);
    }
}
