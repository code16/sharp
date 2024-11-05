<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Data\Form\Fields\Common\FormDynamicAttributeData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class FormAutocompleteLocalFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('FormAutocompleteItemData | { [locale:string]: FormAutocompleteItemData }')]
    public ?array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Autocomplete->value.'"')]
        public FormFieldType $type,
        #[LiteralTypeScriptType('"local"')]
        public string $mode,
        public string $itemIdAttribute,
        #[LiteralTypeScriptType('Array<FormAutocompleteItemData> | FormAutocompleteDynamicLocalValuesData')]
        public array $localValues,
        public ?string $placeholder = null,
        public ?string $listItemTemplate = null,
        public ?string $resultItemTemplate = null,
        #[LiteralTypeScriptType('{ [key:string]: any } | null')]
        public ?array $templateData = null,
        /** @var array<string> */
        public ?array $searchKeys = null,
        public ?bool $localized = null,
        /** @var DataCollection<FormDynamicAttributeData> */
        public ?DataCollection $dynamicAttributes = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {}

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
