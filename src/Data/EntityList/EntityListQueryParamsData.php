<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

#[LiteralTypeScriptType('{
    search?: string,
    page?: number,
    sort?: string,
    dir?: "asc" | "desc",
} & {
    [filterKey: string]: string,
}')]
final class EntityListQueryParamsData extends Data
{
    public function __construct(
        #[Optional]
        public ?string $search = null,
        #[Optional]
        public ?int $page = null,
        #[Optional]
        public ?string $sort = null,
        #[Optional]
        #[LiteralTypeScriptType('"asc" | "desc"')]
        public ?string $dir = null,
        ...$filters,
    ) {
        $this->additional($filters);
    }
    
    public static function from(array $query): self
    {
        return new self(...$query);
    }
}
