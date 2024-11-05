<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class ShowEntityListFieldData extends Data
{
    #[Optional]
    public null $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::EntityList->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public string $entityListKey,
        #[TypeScriptType([
            'instance' => 'string[]',
            'entity' => 'string[]',
        ])]
        public array $hiddenCommands,
        public bool $showEntityState,
        public bool $showReorderButton,
        public bool $showCreateButton,
        public bool $showSearchField,
        public bool $showCount,
        public string $endpointUrl,
        public ?string $label = null,
        /** @var array<string, mixed> */
        public ?array $hiddenFilters = null,
    ) {}

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
