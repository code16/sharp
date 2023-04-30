<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListField
{
    public string $key;
    protected string $label = '';
    protected bool $sortable = false;
    protected bool $html = true;
    protected ?int $width = null;
    protected ?int $widthXs = null;
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

    public function setWidth(int $width = null, int|bool|null $widthOnSmallScreens = null): self
    {
        $this->width = $width;
        if ($widthOnSmallScreens === false) {
            $this->widthXs = null;
            $this->hideOnXs = true;
        } elseif ($widthOnSmallScreens !== null) {
            $this->widthXs = $widthOnSmallScreens;
        }

        return $this;
    }

    public function setWidthOnSmallScreens(int $widthOnSmallScreens = null): self
    {
        $this->widthXs = $widthOnSmallScreens;

        return $this;
    }

    public function hideOnSmallScreens(bool $hideOnSmallScreens = true): self
    {
        $this->hideOnXs = $hideOnSmallScreens;

        return $this;
    }

    public function getFieldProperties(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'html' => $this->html,
        ];
    }

    public function getLayoutProperties(): array
    {
        return [
            'key' => $this->key,
            'size' => $this->width ?: 'fill',
            'hideOnXS' => $this->hideOnXs,
            'sizeXS' => $this->widthXs ?: 'fill',
        ];
    }
}
