<?php

namespace App\Sharp;

use App\Models\Post;
use App\Sharp\Entities\AuthorEntity;
use App\Sharp\Entities\CategoryEntity;
use App\Sharp\Entities\DemoDashboardEntity;
use App\Sharp\Entities\PostEntity;
use App\Sharp\Entities\ProfileEntity;
use App\Sharp\Entities\TestEntity;
use App\Sharp\Utils\Filters\StateFilter;
use Code16\Sharp\Utils\Links\LinkToEntityList;
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
                    ->addEntityLink(
                        entityKeyOrClassName: PostEntity::class,
                        label: 'Posts',
                        icon: 'lucide-file-text',
                        badge: fn () => Post::query()
                            ->where('state', 'draft')
                            ->count() ?: null,
                        badgeTooltip: 'See draft posts',
                        badgeLink: LinkToEntityList::make(PostEntity::class)
                            ->addFilter(StateFilter::class, 'draft'),
                    )
                    ->addEntityLink(CategoryEntity::class, 'Categories', icon: 'lucide-tags')
                    ->addEntityLink(AuthorEntity::class, 'Authors', icon: 'lucide-signature');
            })
            ->addEntityLink(DemoDashboardEntity::class, 'Dashboard', icon: 'lucide-layout-dashboard')
            ->addEntityLink(TestEntity::class, 'Fields test', icon: 'lucide-cog');
    }
}
