<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class EntityListFieldData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('"text"|"state"|"badge"')]
        public string $type,
        public string $key,
        public string $label,
        public bool $sortable,
        public ?string $width,
        public bool $hideOnXS,
        public ?bool $html = null,
        public ?string $tooltip = null,
    ) {}
}
