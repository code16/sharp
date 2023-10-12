<?php

namespace Code16\Sharp\Http\Middleware;

use Code16\Sharp\Data\Filters\GlobalFiltersData;
use Code16\Sharp\Data\MenuData;
use Code16\Sharp\Data\UserData;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'sharp::app';

    public function share(Request $request)
    {
        return [
            ...parent::share($request),
            'sharpVersion' => sharp_version(),
            'locale' => app()->getLocale(),
            'translations' => Cache::rememberForever('sharp.translations.'.sharp_version(), fn () => collect([
                'sharp::action_bar' => __('sharp::action_bar'),
                'sharp::dashboard' => __('sharp::dashboard'),
                'sharp::entity_list' => __('sharp::entity_list'),
                'sharp::filters' => __('sharp::filters'),
                'sharp::form' => __('sharp::form'),
                'sharp::login' => __('sharp::login'),
                'sharp::menu' => __('sharp::menu'),
                'sharp::modals' => __('sharp::modals'),
                'sharp::show' => __('sharp::show'),
            ])->flatMap(fn ($values, $group) => collect($values)->mapWithKeys(fn ($value, $key) => ["$group.$key" => $value])
                )->toArray()
            ),
            'config' => [
                'sharp.auth.suggest_remember_me' => config('sharp.auth.suggest_remember_me', false),
                'sharp.custom_url_segment' => config('sharp.custom_url_segment'),
                'sharp.display_sharp_version_in_title' => config('sharp.display_sharp_version_in_title', true),
                'sharp.display_breadcrumb' => config('sharp.display_breadcrumb', false),
                'sharp.markdown_editor.tight_lists_only' => config('sharp.markdown_editor.tight_lists_only', true),
                'sharp.markdown_editor.nl2br' => config('sharp.markdown_editor.nl2br', false),
                'sharp.name' => config('sharp.name', 'Sharp'),
                'sharp.search.enabled' => value(config('sharp.search.enabled', false)),
                'sharp.search.placeholder' => config('sharp.search.placeholder'),
                'sharp.theme.logo_urls.login' => config('sharp.theme.logo_urls.login'),
                'sharp.theme.logo_urls.menu' => config('sharp.theme.logo_urls.menu'),
            ],
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
