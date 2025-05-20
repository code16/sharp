<?php

namespace App\Providers;

use App\Http\Middleware\PrefillLoginWithExampleCredentials;
use App\Models\Media;
use App\Sharp\AppSearchEngine;
use App\Sharp\Demo2faNotificationHandler;
use App\Sharp\DummyGlobalFilter;
use App\Sharp\SharpMenu;
use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\SharpAppServiceProvider;

class DemoSharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('Demo project')
            ->discoverEntities()
            ->addGlobalFilter(DummyGlobalFilter::class)
            ->configureUploadsThumbnailCreation(uploadModelClass: Media::class)
            ->setSharpMenu(SharpMenu::class)
            ->setThemeColor('#004c9b')
            ->setThemeLogo(logoUrl: '/img/sharp/logo.svg', logoHeight: '1rem', faviconUrl: '/img/sharp/favicon-32x32.png')
            ->enableImpersonation()
            ->enableForgottenPassword()
            ->setAuthCustomGuard('web')
            ->setLoginAttributes('email', 'password')
            ->setUserDisplayAttribute('name')
            ->setUserAvatarAttribute('avatar_url')
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
