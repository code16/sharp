<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    ShowEntityListFieldData::class
//    .'|'.ShowCustomFieldData::class
    .'|'.ShowFileFieldData::class
    .'|'.ShowHtmlFieldData::class
    .'|'.ShowListFieldData::class
    .'|'.ShowPictureFieldData::class
    .'|'.ShowTextFieldData::class
)]
final class ShowFieldData extends Data
{
    public function __construct() {
    }

    public static function from(array $field): Data
    {
        $field['type'] = ShowFieldType::tryFrom($field['type']) ?? $field['type'];

        return match($field['type']) {
            ShowFieldType::EntityList => ShowEntityListFieldData::from($field),
            ShowFieldType::File => ShowFileFieldData::from($field),
            ShowFieldType::Html => ShowHtmlFieldData::from($field),
            ShowFieldType::List => ShowListFieldData::from($field),
            ShowFieldType::Picture => ShowPictureFieldData::from($field),
            ShowFieldType::Text => ShowTextFieldData::from($field),
            default => ShowCustomFieldData::from($field),
        };
    }
}
