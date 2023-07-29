<?php

namespace Code16\Sharp\Http\Middleware;

use Code16\Sharp\Data\MenuData;
use Code16\Sharp\Data\ThemeData;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Code16\Sharp\Utils\SharpTheme;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'sharp::app';

    public function share(Request $request)
    {
        if (! auth()->user()) {
            return parent::share($request);
        }

//        $currentEntityKey = currentSharpRequest()->getCurrentBreadcrumbItem()->key ?? null;
//        $currentEntityItem = $currentEntityKey ? app(SharpMenuManager::class)->getEntityMenuItem($currentEntityKey) : null;

        return array_merge(
            parent::share($request),
            [
                'config' => [
                    'sharp' => [
                        'name' => config('sharp.name', 'Sharp'),
                        'search' => [
                            'enabled' => config('sharp.search.enabled'),
                            'placeholder' => config('sharp.search.placeholder'),
                        ],
                    ],
                ],
                'hasGlobalFilters' => count(value(config('sharp.global_filters')) ?? []) > 0,
                'menu' => MenuData::from(app(SharpMenuManager::class)),
                'theme' => ThemeData::from(app(SharpTheme::class)),
                //                'currentEntity' => $currentEntityItem ? [
                //                    'key' => $currentEntityItem->getEntityKey(),
                //                    'label' => $currentEntityItem->getLabel(),
                //                    'icon' => $currentEntityItem->getIcon(),
                //                ] : null,
            ]
        );
    }
}
