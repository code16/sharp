<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Show\ShowFieldAuthorizationsData;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class ShowDashboardFieldData extends Data
{
    #[Optional]
    public null $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::Dashboard->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public string $dashboardKey,
        /** @var string[] */
        public array $hiddenCommands,
        public string $endpointUrl,
        public ShowFieldAuthorizationsData $authorizations,
        public ?string $label = null,
        /** @var array<string, mixed> */
        public ?array $hiddenFilters = null,
    ) {}

    public static function from(array $field): self
    {
        $field['authorizations'] = ShowFieldAuthorizationsData::from($field['authorizations']);

        return new self(...$field);
    }
}
