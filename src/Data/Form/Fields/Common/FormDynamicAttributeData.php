<?php

namespace Code16\Sharp\Data\Form\Fields\Common;


use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormDynamicAttributeData extends Data
{
    public function __construct(
        public string $name,
        #[LiteralTypeScriptType('"map" | "template"')]
        public string $type,
        /** @var array<string> */
        public ?array $path = null,
        public ?string $default = null,
    ) {
    }

    public static function from(array $dynamicAttribute): self
    {
        return new self(...$dynamicAttribute);
    }
}
