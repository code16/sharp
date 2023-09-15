<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FormAutocompleteFieldMode;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormEditorFieldData extends Data
{
    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Check->value.'"')]
        public FormFieldType $type,
        public int $minHeight,
        public string $placeholder,
        public bool $markdown,
        public bool $inline,
        public bool $showCharacterCount,
        /** @var array<string> */
        public ?array $toolbar = null,
        public ?int $maxHeight = null,
        public ?int $maxLength = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?array $conditionalDisplay = null,
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
