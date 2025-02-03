<?php

namespace Code16\Sharp\Data\Search;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class GlobalSearchData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('{ placeholder: string }')]
        public array $config,
    ) {}

    public static function from(array $globalSearch): self
    {
        return new self(
            config: $globalSearch['config'],
        );
    }
}
