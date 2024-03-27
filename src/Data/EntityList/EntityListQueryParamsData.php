<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

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
    ) {
    }
}
