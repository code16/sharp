<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Data\Show\Fields\ShowTextFieldData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class FormEditorFieldData extends Data
{
    /**
     * Same value that @see ShowTextFieldData
     */
    #[Optional]
    #[LiteralTypeScriptType('{
        text: string | { [locale:string]: string|null } | null,
        uploads?: { [id:string]: { file:FormUploadFieldValueData, legend?: string|null } },
        embeds?: { [embedKey:string]: { [id:string]: EmbedData["value"] } },
    }')]
    public array|null $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Editor->value.'"')]
        public FormFieldType $type,
        public int $minHeight,
        public bool $markdown,
        public bool $inline,
        public bool $showCharacterCount,
        #[TypeScriptType(FormEditorFieldUploadData::class)]
        public ?array $uploads = null,
        #[LiteralTypeScriptType('{ [embedKey:string]:EmbedData }')]
        public ?array $embeds = null,
        #[LiteralTypeScriptType('Array<FormEditorToolbarButton>')]
        public ?array $toolbar = null,
        public ?int $maxHeight = null,
        public ?int $maxLength = null,
        public ?string $placeholder = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
        public ?bool $localized = null,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
