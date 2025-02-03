<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Embeds\EmbedData;
use Code16\Sharp\Data\Form\Fields\FormEditorFieldData;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class ShowTextFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType(FormEditorFieldData::VALUE_TS_TYPE)]
    public string|array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::Text->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public bool $html,
        public ?bool $localized = null,
        public ?int $collapseToWordCount = null,
        /** @var array<string, EmbedData> */
        public ?array $embeds = null,
        public ?string $label = null,
    ) {}

    public static function from(array $field): self
    {
        $field = [
            ...$field,
            'embeds' => isset($field['embeds'])
                ? EmbedData::collection($field['embeds'])
                : null,
        ];

        return new self(...$field);
    }
}
