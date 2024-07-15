<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class GlobalFiltersData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('{ filters: ConfigFiltersData }')]
        public array $config,
        public FilterValuesData $filterValues,
    ) {
    }

    public static function from(array $globalFilters): self
    {
        return new self(
            config: $globalFilters['config'],
            filterValues: FilterValuesData::from($globalFilters['filterValues']),
        );
    }
}
