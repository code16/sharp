<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class DateRangeFilterData extends Data
{
    #[Optional]
    public ?DateRangeFilterValueData $value;

    public function __construct(
        public string $key,
        public ?string $label,
        #[LiteralTypeScriptType('"'.FilterType::DateRange->value.'"')]
        public FilterType $type,
        public bool $required,
        public bool $mondayFirst,
        public string $displayFormat,
        #[LiteralTypeScriptType('Array<{ key:string, label:string }>|null')]
        public ?array $presets,
    ) {
    }

    public static function from(array $filter): self
    {

        return new self(...$filter);
    }
}
