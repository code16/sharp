<?php

namespace Code16\Sharp\View\Components\Utils;

use Code16\Sharp\Utils\Menu\SharpMenuItemSeparator;

class MenuItemSeparator extends MenuItem
{
    public string $type = 'separator';
    public ?string $label;
    public ?string $key = null;

    public function __construct(SharpMenuItemSeparator $itemSeparator)
    {
        $this->label = $itemSeparator->getLabel();
    }

    public function isValid(): bool
    {
        return true;
    }
}
