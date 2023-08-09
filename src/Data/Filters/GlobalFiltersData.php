<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class GlobalFiltersData extends Data
{
    public function __construct(
        public ConfigFiltersData $filters,
    ) {
    }

    public static function from(GlobalFilters $globalFilters): self
    {
        $config = $globalFilters->toArray();

        return new self(
            ConfigFiltersData::from($config['filters']),
        );
    }
}
