<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    FormAutocompleteLocalFieldData::class
    .'|'.FormAutocompleteRemoteFieldData::class
    .'|'.FormCheckFieldData::class
//    .'|'.FormCustomFieldData::class
    .'|'.FormDateFieldData::class
    .'|'.FormEditorFieldData::class
    .'|'.FormGeolocationFieldData::class
    .'|'.FormHtmlFieldData::class
    .'|'.FormListFieldData::class
    .'|'.FormNumberFieldData::class
    .'|'.FormSelectFieldData::class
    .'|'.FormTagsFieldData::class
    .'|'.FormTextFieldData::class
    .'|'.FormTextareaFieldData::class
    .'|'.FormUploadFieldData::class
)]
final class FormFieldData extends Data
{
    public function __construct() {}

    public static function from(array $field): Data
    {
        $field['type'] = FormFieldType::tryFrom($field['type']) ?? $field['type'];
        $field['conditionalDisplay'] = FormConditionalDisplayData::optional($field['conditionalDisplay'] ?? null);

        return match ($field['type']) {
            FormFieldType::Autocomplete => match ($field['mode']) {
                'remote' => FormAutocompleteRemoteFieldData::from($field),
                'local' => FormAutocompleteLocalFieldData::from($field),
            },
            FormFieldType::Check => FormCheckFieldData::from($field),
            FormFieldType::Date => FormDateFieldData::from($field),
            FormFieldType::Editor => FormEditorFieldData::from($field),
            FormFieldType::Geolocation => FormGeolocationFieldData::from($field),
            FormFieldType::Html => FormHtmlFieldData::from($field),
            FormFieldType::List => FormListFieldData::from($field),
            FormFieldType::Number => FormNumberFieldData::from($field),
            FormFieldType::Select => FormSelectFieldData::from($field),
            FormFieldType::Tags => FormTagsFieldData::from($field),
            FormFieldType::Text => FormTextFieldData::from($field),
            FormFieldType::Textarea => FormTextareaFieldData::from($field),
            FormFieldType::Upload => FormUploadFieldData::from($field),
            default => FormCustomFieldData::from($field),
        };
    }
}
