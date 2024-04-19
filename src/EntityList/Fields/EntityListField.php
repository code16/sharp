<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListField implements IsEntityListField
{
    public string $key;
    protected string $label = '';
    protected bool $sortable = false;
    protected bool $html = true;
    protected int|string|null $width = null;
    protected bool $hideOnXs = false;

    public static function make(string $key): self
    {
        $instance = new static();
        $instance->key = $key;

        return $instance;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setSortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function setHtml(bool $html = true): self
    {
        $this->html = $html;

        return $this;
    }

    public function setWidth(int|string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function setWidthFill(): self
    {
        $this->width = 'fill';

        return $this;
    }
    
    public function hideOnSmallScreens(bool $hideOnSmallScreens = true): self
    {
        $this->hideOnXs = $hideOnSmallScreens;
        
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
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'html' => $this->html,
            'width' => $this->width,
            'hideOnXS' => $this->hideOnXs,
        ];
    }
}
