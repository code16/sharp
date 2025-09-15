<?php

namespace Code16\Sharp\Form\Fields\Editor;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;

class EditorTextInputReplacementPreset implements Arrayable
{
    use Conditionable;
    use Macroable;

    public function __construct(
        protected array $replacements,
    ) {}

    public static function typographyFrench(
        ?string $locale = null,
        bool $nbsp = true,
        bool $guillemets = false,
    ): self {
        return (new self([]))
            ->when($nbsp)->add(new EditorTextInputReplacement('/( )[!?:;»]/', ' ', $locale))
            ->when($guillemets)->add(new EditorTextInputReplacement('/(["«][^\n\S])/', '« ', $locale))
            ->when($guillemets)->add(new EditorTextInputReplacement('/[«][^\n\S][^»]+([^\n\S]")/', ' »', $locale));
    }

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
