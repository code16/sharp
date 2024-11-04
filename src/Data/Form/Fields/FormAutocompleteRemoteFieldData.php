<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Data\Form\Fields\Common\FormDynamicAttributeData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class FormAutocompleteRemoteFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('FormAutocompleteItemData | { [locale:string]: FormAutocompleteItemData }')]
    public ?array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Autocomplete->value.'"')]
        public FormFieldType $type,
        #[LiteralTypeScriptType('"remote"')]
        public string $mode,
        public string $itemIdAttribute,
        public int $searchMinChars,
        public int $debounceDelay,
        public string $remoteEndpoint,
        /** @var string[]|null */
        public ?array $callbackLinkedFields = null,
        public ?string $placeholder = null,
        /** @var DataCollection<FormDynamicAttributeData> */
        public ?DataCollection $dynamicAttributes = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {
    }

    public static function from(array $field): self
    {
        $field = [
            ...$field,
            'dynamicAttributes' => isset($field['dynamicAttributes'])
                ? FormDynamicAttributeData::collection($field['dynamicAttributes'])
                : null,
        ];

        return new self(...$field);
    }
}
