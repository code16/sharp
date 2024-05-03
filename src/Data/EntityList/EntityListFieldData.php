<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class EntityListFieldData extends Data
{
    public function __construct(
        public string $type,
        public string $key,
        public string $label,
        public bool $sortable,
        public ?string $width,
        public bool $hideOnXS,
        #[Optional]
        public ?bool $html = null,
        #[Optional]
        public ?string $filterKey = null,
        #[Optional]
        public ?string $filterLabelAttribute = null,
    ) {
    }
}
