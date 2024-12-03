<?php

namespace App\Providers;

use App\Sharp\Entities\TestModelEntity;
use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\SharpAppServiceProvider;
use App\Sharp\SharpMenu;

class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('E2E')
            ->setSharpMenu(SharpMenu::class)
            ->addEntity('test-models', TestModelEntity::class);
    }

    protected function declareAccessGate(): void
    {
//        Gate::define('viewSharp', function ($user) {
//            return $user->is_sharp_admin;
//        });
    }
}
