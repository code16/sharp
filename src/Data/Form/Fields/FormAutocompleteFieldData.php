<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Data\Form\Fields\Common\FormDynamicAttributeData;
use Code16\Sharp\Enums\FormAutocompleteFieldMode;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormAutocompleteFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('string|number|null | { [locale:string]: string|number|null }')]
    public int|string|array|null $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Autocomplete->value.'"')]
        public FormFieldType $type,
        #[LiteralTypeScriptType('"local" | "remote"')]
        public string $mode,
        public string $itemIdAttribute,
        public string $listItemTemplate,
        public string $resultItemTemplate,
        public int $searchMinChars,
        #[LiteralTypeScriptType('Array<{ [key:string]: any }> | FormDynamicOptionsData')]
        public array $localValues,
        public int $debounceDelay,
        public string $dataWrapper,
        public ?string $placeholder = null,
        #[LiteralTypeScriptType('{ [key:string]: any } | null')]
        public ?array $templateData = null,
        /** @var array<string> */
        public ?array $searchKeys = null,
        public ?string $remoteEndpoint = null,
        #[LiteralTypeScriptType('"GET" | "POST" | null')]
        public ?string $remoteMethod = null,
        public ?string $remoteSearchAttribute = null,
        public ?bool $localized = null,
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
