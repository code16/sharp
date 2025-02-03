<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class FormTextareaFieldData extends Data
{
    #[LiteralTypeScriptType('string | null | { [locale:string]: string|null }')]
    public string|array|null $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Textarea->value.'"')]
        public FormFieldType $type,
        public ?int $rows = null,
        public ?string $placeholder = null,
        public ?int $maxLength = null,
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
