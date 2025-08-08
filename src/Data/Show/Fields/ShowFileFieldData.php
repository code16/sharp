<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

/**
 * @internal
 */
final class ShowFileFieldData extends Data
{
    #[Optional]
    #[TypeScriptType([
        'disk' => 'string',
        'name' => 'string',
        'path' => 'string',
        'thumbnail' => 'string',
        'playable_preview_url' => 'string',
        'size' => 'int',
        'mime_type' => 'string',
    ])]
    public array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::File->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public ?string $label = null,
    ) {}

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
