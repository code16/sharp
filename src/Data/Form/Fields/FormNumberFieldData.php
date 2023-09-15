<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FormAutocompleteFieldMode;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormNumberFieldData extends Data
{
    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Check->value.'"')]
        public FormFieldType $type,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?array $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
