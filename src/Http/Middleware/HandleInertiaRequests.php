<?php

namespace Code16\Sharp\Http\Middleware;

use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\Data\Filters\GlobalFiltersData;
use Code16\Sharp\Data\LogoData;
use Code16\Sharp\Data\MenuData;
use Code16\Sharp\Data\UserData;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'sharp::app';

    public function __construct(
        protected Filesystem $filesystem
    ) {
    }

    public function share(Request $request)
    {
        return [
            ...parent::share($request),
            'sharpVersion' => sharpVersion(),
            'locale' => app()->getLocale(),
            'session' => [
                '_token' => session()->token(),
                'status' => session('status'),
                'status_title' => session('status_title'),
            ],
            'translations' => Cache::rememberForever('sharp.translations.'.sharpVersion(), function () {
                return collect([
                    'sharp::action_bar',
                    'sharp::dashboard',
                    'sharp::entity_list',
                    'sharp::filters',
                    'sharp::form',
                    'sharp::menu',
                    'sharp::modals',
                    'sharp::pages/auth/forgot-password',
                    'sharp::pages/auth/login',
                    'sharp::pages/auth/impersonate',
                    'sharp::pages/auth/reset-password',
                    'sharp::show',
                ])
                    ->map(function ($group) {
                        return collect(__($group, [], app()->getFallbackLocale()))
                            ->mapWithKeys(fn ($value, $key) => ["$group.$key" => __("$group.$key")]);
                    })
                    ->collapse()
                    ->toArray();
            }),
            'config' => [
                'sharp.auth.forgotten_password.enabled' => config('sharp.auth.forgotten_password.enabled', false),
                'sharp.auth.login_form.display_app_name' => config('sharp.auth.login_form.display_app_name', true),
                'sharp.auth.login_form.suggest_remember_me' => config('sharp.auth.suggest_remember_me', config('sharp.auth.login_form.suggest_remember_me', false)),
                'sharp.custom_url_segment' => sharpConfig()->get('custom_url_segment'),
                'sharp.display_sharp_version_in_title' => config('sharp.display_sharp_version_in_title', true),
                'sharp.display_breadcrumb' => config('sharp.display_breadcrumb', false),
                'sharp.markdown_editor.tight_lists_only' => config('sharp.markdown_editor.tight_lists_only', true),
                'sharp.markdown_editor.nl2br' => config('sharp.markdown_editor.nl2br', false),
                'sharp.name' => config('sharp.name', 'Sharp'),
                'sharp.search.enabled' => sharpConfig()->get('search.enabled'),
                'sharp.search.placeholder' => sharpConfig()->get('search.placeholder'),
//                'sharp.theme.logo_url' => sharpConfig()->get('theme.logo_url'),
                'sharp.theme.logo_height' => sharpConfig()->get('theme.logo_height'),
            ],
            'logo' => LogoData::optional(transform(
                sharpConfig()->get('theme.logo_url'),
                fn ($url) => $url ? [
                    'svg' => str($url)->startsWith('/') && str($url)->endsWith('.svg') && $this->filesystem->exists(public_path($url))
                        ? $this->filesystem->get(public_path($url))
                        : null,
                    'url' => $url,
                ] : null,
            )),
            'globalFilters' => app(GlobalFilters::class)->isEnabled()
                ? GlobalFiltersData::from(app(GlobalFilters::class))
                : null,
            ...auth()->check() ? [
                'menu' => fn () => MenuData::from(app(SharpMenuManager::class)),
                'auth' => fn () => [
                    'user' => UserData::from(auth()->user()),
                ],
            ] : [],
        ];
    }
}
