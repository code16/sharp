<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu as BaseSharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;

class SharpMenu extends BaseSharpMenu
{
    public function build(): self
    {
        return $this
            ->addSection('Blog', function (SharpMenuItemSection $section) {
                $section
                    ->addEntityLink('posts', 'Posts', 'fas fa-file');
            });
    }
}
