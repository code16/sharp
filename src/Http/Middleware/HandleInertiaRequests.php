<?php

namespace Code16\Sharp\Http\Middleware;

use Code16\Sharp\View\Components\Menu;
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
        $currentEntityItem = $currentEntityKey ? app(Menu::class)->getEntityMenuItem($currentEntityKey) : null;
        
        return array_merge(
            parent::share($request),
            [
                'currentEntity' => $currentEntityItem ? [
                    'key' => $currentEntityItem->getEntityKey(),
                    'label' => $currentEntityItem->getLabel(),
                    'icon' => $currentEntityItem->getIcon(),
                ] : null,
            ]
        );
    }
}
