<?php

namespace Code16\Sharp\Form\Fields\Editor\TextInputReplacement;

use Code16\Sharp\Form\Fields\Editor\TextInputReplacement\Concerns\ReplacesFrench;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;

class EditorTextInputReplacementPreset implements Arrayable
{
    use Conditionable;
    use Macroable;
    use ReplacesFrench;

    public function __construct(
        protected array $replacements = [],
    ) {}

    public function add(EditorTextInputReplacement $replacement): self
    {
        $this->replacements[] = $replacement;

        return $this;
    }

    public function toArray(): array
    {
        return collect($this->replacements)
            ->map(fn (EditorTextInputReplacement $replacement) => $replacement->toArray())
            ->all();
    }
}
