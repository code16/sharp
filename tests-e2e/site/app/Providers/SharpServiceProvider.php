<?php

namespace App\Providers;

use App\Sharp\Entities\TestModelEntity;
use App\Sharp\SharpMenu;
use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\SharpAppServiceProvider;

class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('E2E')
            ->setSharpMenu(SharpMenu::class)
            ->addEntity('test-models', TestModelEntity::class)
            ->enableImpersonation();
    }

    protected function declareAccessGate(): void
    {
        //        Gate::define('viewSharp', function ($user) {
        //            return $user->is_sharp_admin;
        //        });
    }
}
