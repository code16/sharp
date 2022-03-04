<?php

namespace Code16\Sharp\Show\Fields;

class SharpShowTextField extends SharpShowField
{
    const FIELD_TYPE = 'text';

    protected ?string $label = null;
    protected ?int $collapseToWordCount = null;
    protected bool $html = true;

    public static function make(string $key): SharpShowTextField
    {
        return new static($key, static::FIELD_TYPE);
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
            'label'               => $this->label,
            'html'                => $this->html,
            'collapseToWordCount' => $this->collapseToWordCount,
        ]);
    }

    protected function validationRules(): array
    {
        return [
            'collapseToWordCount' => 'int|nullable',
            'html'                => 'bool|required',
        ];
    }
}
