<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;

final class ShowPictureFieldData extends Data
{
    public function __construct(
        public string $key,
        public ShowFieldType $type,
        public bool $emptyVisible,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
