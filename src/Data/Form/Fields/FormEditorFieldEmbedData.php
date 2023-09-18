<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Illuminate\Support\Str;

final class FormEditorFieldEmbedData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public string $tag,
        /** @var array<string> */
        public array $attributes,
        public string $template,
    ) {
    }
}
