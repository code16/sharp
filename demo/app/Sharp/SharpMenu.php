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
            ->setUserMenu(function (SharpMenuUserMenu $userMenu) {
                $userMenu
                    ->addEntityLink('profile', 'Profile')
                    ->addExternalLink('https://sharp.code16.fr/docs', 'Documentation');
            })
            ->addSection('Blog', function (SharpMenuItemSection $section) {
                $section
                    ->setCollapsible(false)
                    ->addEntityLink('posts', 'Posts', icon: 'far-file')
                    ->addEntityLink('categories', 'Categories', icon: 'fas-sitemap')
                    ->addEntityLink('authors', 'Authors', icon: 'far-user');
            })
            ->addEntityLink('dashboard', 'Dashboard', icon: 'fas-chart-line')
            ->addEntityLink('test', 'Fields test', icon: 'fas-cog');
    }
}
