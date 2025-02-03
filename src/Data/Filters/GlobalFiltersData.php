<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class GlobalFiltersData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('{ filters: ConfigFiltersData }')]
        public array $config,
        public FilterValuesData $filterValues,
    ) {}

    public static function from(array $globalFilters): self
    {
        return new self(
            config: $globalFilters['config'],
            filterValues: FilterValuesData::from($globalFilters['filterValues']),
        );
    }
}
