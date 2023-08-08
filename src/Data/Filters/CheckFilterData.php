<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class CheckFilterData extends Data
{
    #[Optional]
    public bool $value;

    public function __construct(
        public string $key,
        public string $label,
        #[LiteralTypeScriptType('"'.FilterType::Check->value.'"')]
        public FilterType $type,
        public ?bool $default,
    ) {
    }

    public static function from(array $filter): self
    {
        return new self(...$filter);
    }
}
