<?php

namespace Code16\Sharp\Http\Middleware;

use Code16\Sharp\Data\MenuData;
use Code16\Sharp\Data\ThemeData;
use Code16\Sharp\Data\UserData;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Code16\Sharp\Utils\SharpTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'sharp::app';

    public function share(Request $request)
    {
//        $currentEntityKey = currentSharpRequest()->getCurrentBreadcrumbItem()->key ?? null;
//        $currentEntityItem = $currentEntityKey ? app(SharpMenuManager::class)->getEntityMenuItem($currentEntityKey) : null;

        return [
            ...parent::share($request),
            'sharpVersion' => sharp_version(),
            'locale' => app()->getLocale(),
            'translations' => Cache::rememberForever('sharp.translations.'.sharp_version(), fn () =>
                collect([
                    'sharp::action_bar' => __('sharp-front::action_bar'),
                    'sharp::dashboard' => __('sharp-front::dashboard'),
                    'sharp::entity_list' => __('sharp-front::entity_list'),
                    'sharp::form' => __('sharp-front::form'),
                    'sharp::modals' => __('sharp-front::modals'),
                    'sharp::show' => __('sharp-front::show'),
                    'sharp::filters' => __('sharp-front::filters'),
                    'sharp::login' => __('sharp::login'),
                    'sharp::menu' => __('sharp::menu'),
                ])->flatMap(fn ($values, $group) =>
                    collect($values)->mapWithKeys(fn ($value, $key) => ["$group.$key" => $value])
                )->toArray()
            ),
            'config' => [
                'sharp.name' => config('sharp.name', 'Sharp'),
                'sharp.search.enabled' => config('sharp.search.enabled'),
                'sharp.search.placeholder' => config('sharp.search.placeholder'),
                'sharp.auth.suggest_remember_me' => config('sharp.auth.suggest_remember_me', false),
            ],
            'hasGlobalFilters' => count(value(config('sharp.global_filters')) ?? []) > 0,
            'theme' => ThemeData::from(app(SharpTheme::class)),
            ...auth()->check() ? [
                'menu' => fn () => MenuData::from(app(SharpMenuManager::class)),
                'auth' => fn () => [
                    'user' => UserData::from(auth()->user()),
                ],
            ] : [],
            //                'currentEntity' => $currentEntityItem ? [
            //                    'key' => $currentEntityItem->getEntityKey(),
            //                    'label' => $currentEntityItem->getLabel(),
            //                    'icon' => $currentEntityItem->getIcon(),
            //                ] : null,
        ];
    }
}
