<?php

namespace Code16\Sharp\Http\Middleware;

use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'sharp::app';

    public function share(Request $request)
    {
        if(!auth()->user()) {
            return parent::share($request);
        }

        $currentEntityKey = currentSharpRequest()->breadcrumb()->first()->key ?? null;
        $currentEntityItem = $currentEntityKey ? app(SharpMenuManager::class)->getEntityMenuItem($currentEntityKey) : null;

        return array_merge(
            parent::share($request),
            [
                'config' => [
                    'search' => [
                        'placeholder' => config('sharp.search.placeholder'),
                    ]
                ],
                'currentEntity' => $currentEntityItem ? [
                    'key' => $currentEntityItem->getEntityKey(),
                    'label' => $currentEntityItem->getLabel(),
                    'icon' => $currentEntityItem->getIcon(),
                ] : null,
            ]
        );
    }
}
