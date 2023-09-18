<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormAutocompleteFieldMode;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormTextFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('string | null | { [locale:string]: string|null }')]
    public string|array|null $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Text->value.'"')]
        public FormFieldType $type,
        #[LiteralTypeScriptType('"text" | "password"')]
        public string $inputType,
        public ?string $placeholder = null,
        public ?int $maxLength = null,
        public ?bool $localized = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
