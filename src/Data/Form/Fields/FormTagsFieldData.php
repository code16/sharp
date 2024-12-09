<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class FormTagsFieldData extends Data
{
    #[LiteralTypeScriptType('Array<{ id:string|number, label: string }> | null')]
    public ?array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Tags->value.'"')]
        public FormFieldType $type,
        public bool $creatable,
        public string $createText,
        #[LiteralTypeScriptType('Array<{ id: string|number, label: string }>')]
        public array $options,
        public ?int $maxTagCount = null,
        public ?string $placeholder = null,
        public ?bool $localized = null,
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
