<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Enums\FilterType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    CheckFilterData::class
    .'|'.DateRangeFilterData::class
    .'|'.SelectFilterData::class
)]
final class FilterData extends Data
{
    public function __construct() {
    }

    public static function from(array $filter)
    {
        $filter['type'] = FilterType::from($filter['type']);

        return match($filter['type']) {
            FilterType::Select => SelectFilterData::from($filter),
            FilterType::DateRange => DateRangeFilterData::from($filter),
            FilterType::Check => CheckFilterData::from($filter),
        };
    }
}
