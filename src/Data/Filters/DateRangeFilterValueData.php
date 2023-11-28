<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class DateRangeFilterValueData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('Date | string')]
        public string $start,
        #[LiteralTypeScriptType('Date | string')]
        public string $end,
    ) {
    }

    public static function from(array $value): self
    {
        return new self(...$value);
    }
}
