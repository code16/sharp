<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Data\Form\Fields\Common\FormDynamicAttributeData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class FormSelectFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('string | number | Array<string|number> | null')]
    public string|int|array|null $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Select->value.'"')]
        public FormFieldType $type,
        #[LiteralTypeScriptType('Array<{ id: string| number, label: string | { [locale:string]: string } }> | FormDynamicOptionsData')]
        public array $options,
        public bool $multiple,
        public bool $showSelectAll,
        public bool $clearable,
        #[LiteralTypeScriptType('"list" | "dropdown"')]
        public string $display,
        public bool $inline,
        /** @var FormDynamicAttributeData[]|null */
        public ?array $dynamicAttributes = null,
        public ?int $maxSelected = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {}

    /**
     * @param  array{key: string, type: FormFieldType, options: array, multiple: bool, showSelectAll: bool, clearable: bool, display: string, inline: bool, dynamicAttributes?: array|null, maxSelected?: int|null, label?: string|null, readOnly?: bool|null, conditionalDisplay?: FormConditionalDisplayData|null, helpMessage?: string|null, extraStyle?: string|null}  $field
     */
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
