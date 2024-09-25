<?php

namespace Code16\Sharp\Http\Middleware;

use Code16\Sharp\Data\Filters\GlobalFiltersData;
use Code16\Sharp\Data\LogoData;
use Code16\Sharp\Data\MenuData;
use Code16\Sharp\Data\SessionData;
use Code16\Sharp\Data\UserData;
use Code16\Sharp\Enums\SessionStatusLevel;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'sharp::app';

    public function __construct(protected Filesystem $filesystem)
    {
    }

    public function share(Request $request)
    {
        return [
            ...parent::share($request),
            'sharpVersion' => sharp()->version(),
            'locale' => app()->getLocale(),
            'session' => SessionData::from([
                '_token' => session()->token(),
                'status' => session('status'),
                'status_title' => session('status_title'),
                'status_level' => session('status_level')
                    ? SessionStatusLevel::from(session('status_level'))
                    : null,
            ]),
            'translations' => Cache::rememberForever('sharp.translations.'.app()->getLocale().'.'.sharp()->version(), function () {
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
                            ->mapWithKeys(fn($value, $key) => ["$group.$key" => __("$group.$key")]);
                    })
                    ->collapse()
                    ->toArray();
            }),
            'config' => [
                'sharp.auth.forgotten_password.enabled' => sharp()->config()->get('auth.forgotten_password.enabled'),
                'sharp.auth.suggest_remember_me' => sharp()->config()->get('auth.suggest_remember_me'),
                'sharp.custom_url_segment' => sharp()->config()->get('custom_url_segment'),
                'sharp.display_sharp_version_in_title' => sharp()->config()->get('display_sharp_version_in_title'),
                'sharp.display_breadcrumb' => sharp()->config()->get('display_breadcrumb'),
                'sharp.name' => sharp()->config()->get('name'),
                'sharp.search.enabled' => sharp()->config()->get('search.enabled'),
                'sharp.search.placeholder' => sharp()->config()->get('search.placeholder'),
                'sharp.theme.logo_height' => sharp()->config()->get('theme.logo_height'),
            ],
            'logo' => LogoData::optional(transform(
                sharp()->config()->get('theme.logo_url'),
                fn($url) => $url ? [
                    'svg' => str($url)->startsWith('/') && str($url)->endsWith('.svg') && $this->filesystem->exists(public_path($url))
                        ? $this->filesystem->get(public_path($url))
                        : null,
                    'url' => $url,
                ] : null,
            )),
            'globalFilters' => app(GlobalFilters::class)->isEnabled()
                ? GlobalFiltersData::from(app(GlobalFilters::class)->toArray())
                : null,
            ...auth()->check() ? [
                'menu' => fn() => MenuData::from(app(SharpMenuManager::class)),
                'auth' => fn() => [
                    'user' => UserData::from(auth()->user()),
                ],
            ] : [],
        ];
    }
}
