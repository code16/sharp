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
                    ->addEntityLink('profile', 'Profile', 'fa fa-user')
                    ->addExternalLink('https://sharp.code16.fr/docs', 'Documentation', 'fa fa-book');
            })
            ->addSection('Blog', function (SharpMenuItemSection $section) {
                $section
                    ->setCollapsible(false)
                    ->addEntityLink('posts', 'Posts', 'fas fa-file')
                    ->addEntityLink('categories', 'Categories', 'fas fa-tags')
                    ->addEntityLink('authors', 'Authors', 'fas fa-users');
            })
            ->addEntityLink('dashboard', 'Dashboard', 'fas fa-chart-line')
            ->addEntityLink('test', 'Fields test', 'fas fa-cog');
    }
}
