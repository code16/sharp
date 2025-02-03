<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

/**
 * @internal
 */
final class FormDateFieldData extends Data
{
    #[Optional]
    public ?string $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Date->value.'"')]
        public FormFieldType $type,
        public bool $hasDate,
        public bool $hasTime,
        public string $minTime,
        public string $maxTime,
        public int $stepTime,
        public bool $mondayFirst,
        public string $language,
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
