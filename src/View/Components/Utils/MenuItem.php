<?php

namespace Code16\Sharp\View\Components\Utils;

use Code16\Sharp\Utils\Menu\SharpMenuItem;

abstract class MenuItem
{
    public ?string $label;

    public static function buildFromItemClass(SharpMenuItem $item): ?MenuItem
    {
        if ($item->isSection()) {
            $menuItem = new MenuItemSection($item);
        } elseif ($item->isEntity()) {
            $menuItem = new MenuItemEntity($item);
        } elseif ($item->isExternalLink()) {
            $menuItem = new MenuItemUrl($item);
        } elseif ($item->isSeparator()) {
            $menuItem = new MenuItemSeparator($item);
        }

        return ($menuItem ?? false) && $menuItem->isValid()
            ? $menuItem
            : null;
    }

    public function isMenuItemSection(): bool
    {
        return false;
    }

    public function isMenuItemEntity(): bool
    {
        return false;
    }

    public function isMenuItemUrl(): bool
    {
        return false;
    }

    public function isMenuItemDashboard(): bool
    {
        return false;
    }

    abstract public function isValid(): bool;
}
