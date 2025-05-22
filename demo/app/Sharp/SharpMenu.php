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
                        PostEntity::class, 'Posts',
                        icon: 'lucide-file-text',
                        // badge: new class extends SharpBadge {
                        //     public function getFilters(): array
                        //     {
                        //         return [
                        //             StateFilter::class => 'draft',
                        //         ];
                        //     }
                        //     public function label()
                        //     {
                        //         return Post::query()
                        //             ->where('state', 'draft')
                        //             ->count();
                        //     }
                        // }
                    )
                    ->addEntityLink(CategoryEntity::class, 'Categories', icon: 'lucide-tags')
                    ->addEntityLink(AuthorEntity::class, 'Authors', icon: 'lucide-signature');
            })
            ->addEntityLink(DemoDashboardEntity::class, 'Dashboard', icon: 'lucide-layout-dashboard')
            ->addEntityLink(TestEntity::class, 'Fields test', icon: 'lucide-cog');
    }
}
