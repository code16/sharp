<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListField implements IsEntityListField
{
    use HasCommonEntityListFieldAttributes;

    public string $key;
    protected bool $html = true;

    public static function make(string $key): self
    {
        $instance = new static();
        $instance->key = $key;

        return $instance;
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

    public function getFieldProperties(): array
    {
        return [
            'type' => 'text',
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'html' => $this->html,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
        ];
    }
}
