<?php

namespace Code16\Sharp\Form\Fields\Editor;

use Illuminate\Contracts\Support\Arrayable;

class EditorTextInputReplacement implements Arrayable
{
    public function __construct(
        protected string $pattern,
        protected string $replacement,
        protected ?string $locale = null,
    ) {}

    public function toArray(): array
    {
        return [
            'pattern' => $this->pattern,
            'replacement' => $this->replacement,
            'locale' => $this->locale,
        ];
    }
}
