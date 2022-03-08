<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu as BaseSharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;

class SharpMenu extends BaseSharpMenu
{
    public function build(): self
    {
        return $this
            ->addEntityLink('dashboard', 'Dashboard', 'fas fa-meter')
            ->addSection('Blog', function (SharpMenuItemSection $section) {
                $section
                    ->addEntityLink('posts', 'Posts', 'fas fa-file')
                    ->addEntityLink('categories', 'Categories', 'fas fa-tags')
                    ->addEntityLink('authors', 'Authors', 'fas fa-users');
            })
            ->addEntityLink('profile', 'Profile', 'fa fa-user')
            ->addEntityLink('test', 'Fields test', 'fas fa-cog');
    }
}
