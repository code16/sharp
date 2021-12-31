<?php

namespace Code16\Sharp\Utils\Menu;

class SharpMenuSection extends SharpMenuItem
{
    use HasSharpMenuItems;
    
    public function __construct(string $label)
    {
        parent::__construct($label, null);
    }

    public function addSeparator(string $label): self
    {
        return $this;
    }

    public function isSection(): bool
    {
        return true;
    }

    public function isEntity(): bool
    {
        return false;
    }
}