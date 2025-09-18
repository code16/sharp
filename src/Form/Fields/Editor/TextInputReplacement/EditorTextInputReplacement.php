<?php

namespace Code16\Sharp\Form\Fields\Editor\TextInputReplacement;

use Illuminate\Contracts\Support\Arrayable;

class EditorTextInputReplacement implements Arrayable
{
    /**
     * @throws \Exception
     */
    public function __construct(
        protected string $pattern,
        protected string $replacement,
        protected ?string $locale = null,
    ) {
        if(!str_starts_with($pattern, '/') || !str_ends_with($pattern, '/')) {
            throw new \Exception("The replacement pattern \"$pattern\" must start and end with a slash");
        }
    }

    public function toArray(): array
    {
        return [
            'pattern' => $this->pattern,
            'replacement' => $this->replacement,
            'locale' => $this->locale,
        ];
    }
}
