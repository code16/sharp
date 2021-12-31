<?php

namespace Code16\Sharp\Utils\Menu;

class SharpMenuItemSeparator extends SharpMenuItem
{
    public function __construct(protected string $label)
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isSeparator(): bool
    {
        return true;
    }
}