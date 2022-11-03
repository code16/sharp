<?php

namespace Code16\Sharp\Utils\Menu;

class SharpMenuItemSection extends SharpMenuItem
{
    use HasSharpMenuItems;
    
    protected bool $collapsible = true;

    public function __construct(protected string $label)
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }
    
    public function isCollapsible(): bool
    {
        return $this->collapsible;
    }

    public function addSeparator(string $label): self
    {
        $this->items[] = new SharpMenuItemSeparator($label);

        return $this;
    }
    
    public function setCollapsible(bool $collapsible): self
    {
        $this->collapsible = $collapsible;
        
        return $this;
    }

    public function isSection(): bool
    {
        return true;
    }
}
