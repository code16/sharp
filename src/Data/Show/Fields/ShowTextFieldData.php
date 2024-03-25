<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Embeds\EmbedData;
use Code16\Sharp\Data\Form\Fields\FormEditorFieldData;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class ShowTextFieldData extends Data
{
    /**
     * Same value that @see FormEditorFieldData
     */
    #[Optional]
    #[LiteralTypeScriptType('{
        text: string | { [locale:string]: string|null } | null,
        uploads?: Array<{ file:FormUploadFieldValueData, legend?: string|null }>,
        embeds?: { [embedKey:string]:Array<FormData["data"]> },
    }')]
    public string|array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::Text->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public bool $html,
        public ?bool $localized = null,
        public ?int $collapseToWordCount = null,
        /** @var DataCollection<string, EmbedData> */
        public ?DataCollection $embeds = null,
        public ?string $label = null,
    ) {
    }

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
