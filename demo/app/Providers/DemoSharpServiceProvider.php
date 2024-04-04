<?php

namespace App\Providers;

use App\Sharp\AppSearchEngine;
use App\Sharp\DummyGlobalFilter;
use App\Sharp\Entities\AuthorEntity;
use App\Sharp\Entities\CategoryEntity;
use App\Sharp\Entities\DemoDashboardEntity;
use App\Sharp\Entities\PostBlockEntity;
use App\Sharp\Entities\PostEntity;
use App\Sharp\Entities\ProfileEntity;
use App\Sharp\Entities\TestEntity;
use App\Sharp\SharpMenu;
use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\SharpAppServiceProvider;

class DemoSharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('Demo project')
//            ->setCustomUrlSegment('demo-sharp')
            ->addEntity('posts', PostEntity::class)
            ->addEntity('blocks', PostBlockEntity::class)
            ->addEntity('categories', CategoryEntity::class)
            ->addEntity('authors', AuthorEntity::class)
            ->addEntity('profile', ProfileEntity::class)
            ->addEntity('dashboard', DemoDashboardEntity::class)
            ->addEntity('test', TestEntity::class)
            ->when(
                auth()->id() === 1,
                fn (SharpConfigBuilder $config) => $config->addGlobalFilter(DummyGlobalFilter::class)
            )
            ->setLeftMenu(SharpMenu::class)
            ->enableGlobalSearch(AppSearchEngine::class, 'Search for posts or authors...');
    }
}