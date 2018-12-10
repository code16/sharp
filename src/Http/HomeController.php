<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Http\Composers\Utils\MenuItem;

class HomeController extends SharpProtectedController
{

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        foreach (config("sharp.menu", []) as $menuItemConfig) {
            if($menuItem = MenuItem::parse($menuItemConfig)) {
                if($menuItem->isMenuItemCategory()) {
                    foreach($menuItem->entities as $menuItemEntity) {
                        if($menuItemEntity->isMenuItemEntity()) {
                            return redirect()->route('code16.sharp.list', $menuItemEntity->key);
                        }

                        if($menuItemEntity->isMenuItemDashboard()) {
                            return redirect()->route('code16.sharp.dashboard', $menuItemEntity->key);
                        }
                    }
                }

                if($menuItem->isMenuItemEntity()) {
                    return redirect()->route('code16.sharp.list', $menuItem->key);
                }

                if($menuItem->isMenuItemDashboard()) {
                    return redirect()->route('code16.sharp.dashboard', $menuItem->key);
                }
            }
        }

        return view("sharp::welcome");
    }
}