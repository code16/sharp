<?php

namespace Code16\Sharp\Utils\Menu;

class SharpMenuItemSection extends SharpMenuItem
{
    use HasSharpMenuItems;
    
    public function __construct(protected string $label)
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function addSeparator(string $label): self
    {
        $this->items[] = new SharpMenuItemSeparator($label);
        return $this;
    }

    public function isSection(): bool
    {
        return true;
    }
}