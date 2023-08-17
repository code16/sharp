<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

#[TypeScriptType(
    ShowEntityListFieldData::class
    .'|'.ShowFileFieldData::class
    .'|'.ShowHtmlFieldData::class
    .'|'.ShowListFieldData::class
    .'|'.ShowPictureFieldData::class
    .'|'.ShowTextFieldData::class
)]
final class ShowFieldData extends Data
{
    public function __construct(
        public string $key,
        public ShowFieldType $type,
        public bool $emptyVisible,
    ) {
    }

    public static function from(array $field)
    {
        $field['type'] = ShowFieldType::from($field['type']);

        return match($field['type']) {
            ShowFieldType::EntityList => ShowEntityListFieldData::from($field),
            ShowFieldType::File => ShowFileFieldData::from($field),
            ShowFieldType::Html => ShowHtmlFieldData::from($field),
            ShowFieldType::List => ShowListFieldData::from($field),
            ShowFieldType::Picture => ShowPictureFieldData::from($field),
            ShowFieldType::Text => ShowTextFieldData::from($field),
        };
    }
}
