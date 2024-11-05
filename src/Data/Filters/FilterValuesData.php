<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FilterValuesData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('{ [key: string]: any }')]
        public array $current,
        #[LiteralTypeScriptType('{ [key: string]: any }')]
        public array $default,
        #[LiteralTypeScriptType('{ [key: string]: boolean }')]
        public array $valuated,
    ) {}

    public static function from(array $filterValues): self
    {
        return new self(...$filterValues);
    }
}
