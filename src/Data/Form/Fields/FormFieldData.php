<?php

namespace Code16\Sharp\Data\Form\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Data\Form\Fields\Common\FormDynamicAttributeData;
use Code16\Sharp\Data\Show\Fields\ShowEntityListFieldData;
use Code16\Sharp\Data\Show\Fields\ShowFileFieldData;
use Code16\Sharp\Data\Show\Fields\ShowHtmlFieldData;
use Code16\Sharp\Data\Show\Fields\ShowListFieldData;
use Code16\Sharp\Data\Show\Fields\ShowPictureFieldData;
use Code16\Sharp\Data\Show\Fields\ShowTextFieldData;
use Code16\Sharp\Enums\FormFieldType;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    FormAutocompleteFieldData::class
    .'|'.FormCheckFieldData::class
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
    public function __construct() {
    }

    public static function from(array $field): mixed
    {
        $field['type'] = FormFieldType::from($field['type']);
        $field['conditionalDisplay'] = FormConditionalDisplayData::optional($field['conditionalDisplay'] ?? null);

        return match($field['type']) {
            FormFieldType::Autocomplete => FormAutocompleteFieldData::from($field),
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
        };
    }
}
