<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EmbeddedFieldAuthorizationsData;
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
        public EmbeddedFieldAuthorizationsData $authorizations,
        public ?string $label = null,
        /** @var array<string, mixed> */
        public ?array $hiddenFilters = null,
    ) {}

    public static function from(array $field): self
    {
        $field['authorizations'] = EmbeddedFieldAuthorizationsData::from($field['authorizations']);

        return new self(...$field);
    }
}
