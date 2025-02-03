<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

#[LiteralTypeScriptType('{ _root: Array<FilterData> } & { [key: string]: Array<FilterData> }')]
/**
 * @internal
 */
final class ConfigFiltersData extends Data
{
    public function __construct(
        protected array $filters,
    ) {}

    public static function from(array $configFilters): self
    {
        return new self(
            collect($configFilters)
                ->map(fn (array $filters) => FilterData::collection($filters))
                ->toArray()
        );
    }

    public function toArray(): array
    {
        return $this->filters;
    }
}
