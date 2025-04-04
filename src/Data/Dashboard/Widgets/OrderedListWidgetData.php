<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\WidgetType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class OrderedListWidgetData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType([
        'key' => 'string',
        'data' => 'Array<{ label:string, url?:string, count?:number }>',
    ])]
    public array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.WidgetType::OrderedList->value.'"')]
        public WidgetType $type,
        public ?string $title,
        public bool $html,
        public ?string $link = null,
    ) {}

    public static function from(array $widget): self
    {
        return new self(...$widget);
    }
}
