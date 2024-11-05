<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class FormHtmlFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('{ [key:string]: any } | null')]
    public ?array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Html->value.'"')]
        public FormFieldType $type,
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
