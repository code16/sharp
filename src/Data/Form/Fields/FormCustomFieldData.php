<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormAutocompleteFieldMode;
use Code16\Sharp\Enums\FormFieldType;
use Illuminate\Support\Arr;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
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
    ) {
    }

    public static function from(array $field): self
    {
        $commonAttributes = Arr::only($field, [
            'key',
            'type',
            'label',
            'readOnly',
            'conditionalDisplay',
            'helpMessage',
            'extraStyle',
        ]);

        return (new self(...$commonAttributes))
            ->additional(Arr::except($field, array_keys($commonAttributes)));
    }
}
