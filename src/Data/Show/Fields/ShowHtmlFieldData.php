<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;

final class ShowHtmlFieldData extends Data
{
    public function __construct(
        public string $key,
        public ShowFieldType $type,
        public bool $emptyVisible,
        public string $template,
        /** @var array<string,mixed> */
        public ?array $templateData = null,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
