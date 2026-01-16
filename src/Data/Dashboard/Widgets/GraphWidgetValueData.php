<?php

namespace Code16\Sharp\Data\Dashboard\Widgets;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class GraphWidgetValueData extends Data
{
    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('Array<{ label:string, data:number[], color:string }>')]
        public array $datasets,
        /** @var string[] */
        public array $labels,
    ) {}
}
