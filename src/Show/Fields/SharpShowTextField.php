<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Show\Fields\Formatters\TextFieldFormatter;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithEmbeds;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use Code16\Sharp\Utils\Fields\SharpFieldWithLocalization;
use Code16\Sharp\Utils\Fields\SharpFieldWithEmbeds;

class SharpShowTextField extends SharpShowField implements IsSharpFieldWithLocalization, IsSharpFieldWithEmbeds
{
    use SharpFieldWithLocalization;
    use SharpFieldWithEmbeds;

    const FIELD_TYPE = 'text';

    protected ?string $label = null;
    protected ?int $collapseToWordCount = null;
    protected bool $html = true;

    public static function make(string $key): SharpShowTextField
    {
        return new static($key, static::FIELD_TYPE, new TextFieldFormatter());
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function collapseToWordCount(int $wordCount): self
    {
        $this->collapseToWordCount = $wordCount;

        return $this;
    }

    public function doNotCollapse(): self
    {
        $this->collapseToWordCount = null;

        return $this;
    }

    public function setHtml(bool $html = true): self
    {
        $this->html = $html;

        return $this;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'label' => $this->label,
            'html' => $this->html,
            'collapseToWordCount' => $this->collapseToWordCount,
            'localized' => $this->localized,
            'embeds' => $this->innerComponentEmbedsConfiguration(false) ?: null,
        ]);
    }

    protected function validationRules(): array
    {
        return [
            'collapseToWordCount' => 'int|nullable',
            'html' => 'bool|required',
        ];
    }
}
