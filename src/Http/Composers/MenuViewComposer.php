<?php

namespace Code16\Sharp\Http\Composers;

use Code16\Sharp\Http\Composers\Utils\MenuItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class MenuViewComposer
{

    /**
     * Build the menu and bind it to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $menuItems = new Collection;

        foreach (config("sharp.menu", []) as $menuItemConfig) {
            if($menuItem = MenuItem::parse($menuItemConfig)) {
                $menuItems->push($menuItem);
            }
        }

        $view->with('sharpMenu', (object)[
            "name" => config("sharp.name", "Sharp"),
            "user" => sharp_user()->{config("sharp.auth.display_attribute", "name")},
            "menuItems" => $menuItems,
            "currentEntity" => isset($view->entityKey)
                ? explode(':', $view->entityKey)[0]
                : ($view->dashboardKey ?? null)
        ]);

        $view->with('hasGlobalFilters', sizeof(config('sharp.global_filters') ?? []) > 0);
    }
}