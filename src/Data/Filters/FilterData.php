<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    AutocompleteRemoteFilterData::class
    .'|'.CheckFilterData::class
    .'|'.DateRangeFilterData::class
    .'|'.SelectFilterData::class
)]
/**
 * @internal
 */
final class FilterData extends Data
{
    public function __construct() {}

    public static function from(array $filter): Data
    {
        $filter['type'] = FilterType::from($filter['type']);

        return match ($filter['type']) {
            FilterType::AutocompleteRemote => AutocompleteRemoteFilterData::from($filter),
            FilterType::Check => CheckFilterData::from($filter),
            FilterType::DateRange => DateRangeFilterData::from($filter),
            FilterType::Select => SelectFilterData::from($filter),
        };
    }
}
