<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\View\Components\Menu;

class HomeController extends SharpProtectedController
{
    public function index()
    {
        if ($firstEntityUrl = $this->getFirstConfiguredEntityUrl()) {
            return redirect()->to($firstEntityUrl);
        }

        return view('sharp::welcome');
    }

    private function getFirstConfiguredEntityUrl(): ?string
    {
        if ($menuItem = app(Menu::class)->getItems()[0] ?? null) {
            if ($menuItem->isMenuItemEntity() || $menuItem->isMenuItemDashboard()) {
                return $menuItem->url;
            }

            if ($menuItem->isMenuItemCategory()) {
                foreach ($menuItem->entities as $menuItemEntity) {
                    if ($menuItemEntity->isMenuItemEntity() || $menuItemEntity->isMenuItemDashboard()) {
                        return $menuItemEntity->url;
                    }
                }
            }
        }

        return null;
    }
}
