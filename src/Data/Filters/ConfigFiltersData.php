<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Enums\FilterType;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[LiteralTypeScriptType('{ _root: Array<FilterData> } & { [key: string]: Array<FilterData> }')]
final class ConfigFiltersData implements Arrayable
{
    public function __construct(
        protected array $filters,
    ) {
    }

    public static function from(array $filters): self
    {
        return new self(
            collect($filters)
                ->map(fn (array $filters) => FilterData::collection($filters))
                ->all()
        );
    }

    public function toArray(): array
    {
        return $this->filters;
    }
}
