<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormAutocompleteFieldMode;
use Code16\Sharp\Enums\FormFieldType;
use Code16\Sharp\Enums\ShowFieldType;
use Illuminate\Support\Arr;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class ShowCustomFieldData extends Data
{
    #[Optional]
    public mixed $value;

    public function __construct(
        public string $key,
        public string $type,
        public bool $emptyVisible,
    ) {
    }

    public static function from(array $field): self
    {
        $commonAttributes = Arr::only($field, [
            'key',
            'type',
            'emptyVisible'
        ]);

        return (new self(...$commonAttributes))
            ->additional(Arr::except($field, array_keys($commonAttributes)));
    }
}
