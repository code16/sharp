<?php

namespace Code16\Sharp\EntityList\Fields;

class EntityListField
{
    protected string $key;
    protected string $label = '';
    protected bool $sortable = false;
    protected bool $html = true;

    public static function make(string $key)
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

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'sortable' => $this->sortable,
            'html' => $this->html,
        ];
    }
}
