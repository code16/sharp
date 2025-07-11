<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class AutocompleteRemoteFilterData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('{ id: string|number, label: string }')]
    public mixed $value;

    public function __construct(
        public string $key,
        public ?string $label,
        #[LiteralTypeScriptType('"'.FilterType::AutocompleteRemote->value.'"')]
        public FilterType $type,
        // public bool $multiple,
        public bool $required,
        public bool $master,
        public int $debounceDelay,
        public int $searchMinChars,
    ) {}

    public static function from(array $filter): self
    {
        return new self(...$filter);
    }
}
