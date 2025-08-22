<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Show\Fields\Formatters\SharpShowFieldFormatter;
use Code16\Sharp\Show\Fields\Formatters\TextFormatter;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithEmbeds;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use Code16\Sharp\Utils\Fields\SharpFieldWithEmbeds;
use Code16\Sharp\Utils\Fields\SharpFieldWithLocalization;
use Code16\Sharp\Utils\Sanitization\IsSharpFieldWithHtmlSanitization;
use Code16\Sharp\Utils\Sanitization\SharpFieldWithHtmlSanitization;

class SharpShowTextField extends SharpShowField implements IsSharpFieldWithEmbeds, IsSharpFieldWithHtmlSanitization, IsSharpFieldWithLocalization
{
    use SharpFieldWithEmbeds;
    use SharpFieldWithHtmlSanitization;
    use SharpFieldWithLocalization;

    const FIELD_TYPE = 'text';

    protected ?string $label = null;
    protected ?int $collapseToWordCount = null;
    protected bool $html = true;

    protected function __construct(string $key, string $type, ?SharpShowFieldFormatter $formatter = null)
    {
        parent::__construct($key, $type, $formatter);
        $this->sanitizeHtml = true;
    }

    public static function make(string $key): SharpShowTextField
    {
        return new static($key, static::FIELD_TYPE, new TextFormatter());
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
            'sanitize' => $this->sanitizeHtml,
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
