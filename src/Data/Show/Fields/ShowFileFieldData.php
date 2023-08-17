<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class ShowFileFieldData extends Data
{
    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::File->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public ?string $label,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
