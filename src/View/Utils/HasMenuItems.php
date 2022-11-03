<?php

namespace Code16\Sharp\View\Utils;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\View\Components\Menu\MenuSection;

trait HasMenuItems
{
    public function isItemVisible(SharpMenuItem $item): bool
    {
        if ($item->isSection()) {
            return count((new MenuSection($item))->getItems()) > 0;
        }
        if ($item->isEntity()) {
            return sharp_has_ability('entity', $item->getKey());
        }

        return true;
    }
}
