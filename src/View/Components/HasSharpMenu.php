<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Utils\Menu\SharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuUserMenu;

trait HasSharpMenu
{
    private ?SharpMenu $sharpMenu = null;

    private function getSharpMenu(): ?SharpMenu
    {
        if ($this->sharpMenu === null) {
            if (($sharpMenu = config('sharp.menu')) === null) {
                return null;
            }

            $this->sharpMenu = is_string($sharpMenu)
                ? app($sharpMenu)
                : $sharpMenu;

            $this->sharpMenu->build();
        }

        return $this->sharpMenu;
    }
}
