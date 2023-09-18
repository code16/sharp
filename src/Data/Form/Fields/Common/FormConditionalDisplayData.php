<?php

namespace Code16\Sharp\Data\Form\Fields\Common;


use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormConditionalDisplayData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('"and" | "or"')]
        public string $operator,
        #[LiteralTypeScriptType('Array<{ key: string, values: string|boolean|Array<string> }>')]
        public array $fields,
    ) {
    }

    public static function from(array $dynamicAttribute): self
    {
        return new self($dynamicAttribute);
    }
}
