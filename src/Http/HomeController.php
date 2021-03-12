<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\View\Components\Menu;

class HomeController extends SharpProtectedController
{
    public function index()
    {
        if($firstEntityUrl = $this->getFirstConfiguredEntityUrl()) {
            return redirect()->to($firstEntityUrl);
        }

        return view("sharp::welcome");
    }

    private function getFirstConfiguredEntityUrl(): ?string
    {
        if($menuItem = app(Menu::class)->getItems()[0] ?? null) {
            if($menuItem->isMenuItemEntity()) {
                return route('code16.sharp.list', $menuItem->key);
            }

            if($menuItem->isMenuItemDashboard()) {
                return route('code16.sharp.dashboard', $menuItem->key);
            }
            
            if($menuItem->isMenuItemCategory()) {
                foreach($menuItem->entities as $menuItemEntity) {
                    if($menuItemEntity->isMenuItemEntity()) {
                        return route('code16.sharp.list', $menuItemEntity->key);
                    }

                    if($menuItemEntity->isMenuItemDashboard()) {
                        return route('code16.sharp.dashboard', $menuItemEntity->key);
                    }
                }
            }
        }
        
        return null;
    }
}