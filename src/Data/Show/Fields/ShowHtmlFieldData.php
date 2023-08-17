<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class ShowHtmlFieldData extends Data
{
    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::Html->value.'"')]
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
