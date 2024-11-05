<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class FormCustomFieldData extends Data
{
    #[Optional]
    public mixed $value;

    public function __construct(
        public string $key,
        public string $type,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
        ...$additionalOptions,
    ) {
        $this->additional($additionalOptions);
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
