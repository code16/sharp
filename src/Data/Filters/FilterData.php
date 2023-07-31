<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Enums\FilterType;

final class FilterData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public FilterType $type,
        public mixed $default,
    ) {
    }

    public static function from(array $filter)
    {
        $filter['type'] = FilterType::from($filter['type']);

        return match($filter['type']) {
            FilterType::Select => SelectFilterData::from($filter),
            FilterType::DateRange => DateRangeFilterData::from($filter),
            FilterType::Check => new CheckFilterData(...$filter),
        };
    }
}
