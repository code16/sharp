<?php

namespace App\Providers;

use App\Http\Middleware\PrefillLoginWithExampleCredentials;
use App\Models\Media;
use App\Sharp\AppSearchEngine;
use App\Sharp\Demo2faNotificationHandler;
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
            ->addEntity('posts', PostEntity::class)
            ->addEntity('blocks', PostBlockEntity::class)
            ->addEntity('categories', CategoryEntity::class)
            ->addEntity('authors', AuthorEntity::class)
            ->addEntity('profile', ProfileEntity::class)
            ->addEntity('dashboard', DemoDashboardEntity::class)
            ->addEntity('test', TestEntity::class)
            ->addGlobalFilter(DummyGlobalFilter::class)
            ->configureUploadsThumbnailCreation(uploadModelClass: Media::class)
            ->setSharpMenu(SharpMenu::class)
            ->setThemeColor('#004c9b')
            ->setThemeLogo(logoUrl: '/img/sharp/logo.svg', logoHeight: '1rem', faviconUrl: '/img/sharp/favicon-32x32.png')
//            ->redirectLoginToUrl('/my-login')
            ->enableImpersonation()
            ->enableForgottenPassword()
            ->setAuthCustomGuard('web')
            ->setLoginAttributes('email', 'password')
            ->setUserDisplayAttribute('name')
            ->enable2faCustom(Demo2faNotificationHandler::class)
            ->enableLoginRateLimiting(maxAttempts: 3)
            ->suggestRememberMeOnLoginForm()
            ->appendMessageOnLoginForm(view('sharp._login-page-message'))
            ->enableGlobalSearch(AppSearchEngine::class, 'Search for posts or authors...')
            ->appendToMiddlewareWebGroup(PrefillLoginWithExampleCredentials::class)
            ->loadViteAssets([
                'resources/css/sharp-extension.css',
            ]);
    }
}
