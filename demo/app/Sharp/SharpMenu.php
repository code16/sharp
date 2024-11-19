<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu as BaseSharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;
use Code16\Sharp\Utils\Menu\SharpMenuUserMenu;

class SharpMenu extends BaseSharpMenu
{
    public function build(): self
    {
        return $this
            ->setVisible(false)
            ->setUserMenu(function (SharpMenuUserMenu $userMenu) {
                $userMenu
                    ->addEntityLink('profile', 'Profile')
                    ->addExternalLink('https://sharp.code16.fr/docs', 'Documentation');
            })
            ->addSection('Blog', function (SharpMenuItemSection $section) {
                $section
                    ->setCollapsible(true)
                    ->addEntityLink('posts', 'Posts')
                    ->addSeparator('')
                    ->addEntityLink('categories', 'Categories')
                    ->addEntityLink('authors', 'Authors');
            })
            ->addEntityLink('dashboard', 'Dashboard', icon: 'fas-chart-line')
            ->addEntityLink('test', 'Fields test', icon: 'fas-cog');
    }
}
