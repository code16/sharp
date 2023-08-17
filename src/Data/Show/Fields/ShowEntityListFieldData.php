<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class ShowEntityListFieldData extends Data
{
    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::EntityList->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public string $entityListKey,
        /** @var string[] */
        public array $hiddenFilters,
        /** @var string[] */
        public array $hiddenCommands,
        public bool $showEntityState,
        public bool $showReorderButton,
        public bool $showCreateButton,
        public bool $showSearchField,
        public bool $showCount,
        public ?string $label,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
