<?php

namespace App\Sharp;

use App\Sharp\Entities\AuthorEntity;
use App\Sharp\Entities\CategoryEntity;
use App\Sharp\Entities\DemoDashboardEntity;
use App\Sharp\Entities\PostEntity;
use App\Sharp\Entities\ProfileEntity;
use App\Sharp\Entities\TestEntity;
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
                    ->addEntityLink(ProfileEntity::class, 'Profile')
                    ->addExternalLink('https://sharp.code16.fr/docs', 'Documentation');
            })
            ->addSection('Blog', function (SharpMenuItemSection $section) {
                $section
                    ->setCollapsible(false)
                    ->addEntityLink(PostEntity::class, 'Posts', icon: 'far-file')
                    ->addEntityLink(CategoryEntity::class, 'Categories', icon: 'fas-sitemap')
                    ->addEntityLink(AuthorEntity::class, 'Authors', icon: 'far-user');
            })
            ->addEntityLink(DemoDashboardEntity::class, 'Dashboard', icon: 'fas-chart-line')
            ->addEntityLink(TestEntity::class, 'Fields test', icon: 'fas-cog');
    }
}
