<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class FormListFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('Array<{ [key:string]: Exclude<FormFieldData["value"], FormListFieldData> }> | null')]
    public ?array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::List->value.'"')]
        public FormFieldType $type,
        public bool $addable,
        public bool $removable,
        public bool $sortable,
        public string $itemIdAttribute,
        #[LiteralTypeScriptType('{ [key:string]: FormFieldData }')]
        public array $itemFields,
        public string $addText,
        public ?int $maxItemCount = null,
        public ?string $bulkUploadField = null,
        public ?int $bulkUploadLimit = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {}

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
