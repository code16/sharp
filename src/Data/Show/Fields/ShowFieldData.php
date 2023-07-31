<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;

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
            ShowFieldType::Text => ShowTextFieldData::from($field),
            ShowFieldType::Picture => ShowPictureFieldData::from($field),
        };
    }
}
