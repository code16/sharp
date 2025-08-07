<?php

namespace Code16\Sharp\EntityList\Fields;

use Code16\Sharp\Utils\Sanitization\IsSharpFieldWithHtmlSanitization;
use Code16\Sharp\Utils\Sanitization\SharpFieldWithHtmlSanitization;
use Illuminate\Contracts\Support\Arrayable;

class EntityListField implements Arrayable, IsEntityListField, IsSharpFieldWithHtmlSanitization
{
    use HasCommonEntityListFieldAttributes;
    use SharpFieldWithHtmlSanitization;

    protected bool $html = true;

    private function __construct(string $key)
    {
        $this->key = $key;
        $this->sanitize = true;
    }

    public static function make(string $key): self
    {
        return new static($key);
    }

    public function setHtml(bool $html = true): self
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @deprecated
     */
    public function setWidthOnSmallScreens(int $widthOnSmallScreens): self
    {
        return $this;
    }

    /**
     * @deprecated
     */
    public function setWidthOnSmallScreensFill(): self
    {
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => 'text',
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'html' => $this->html,
            'sanitize' => $this->sanitize,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
        ];
    }
}
