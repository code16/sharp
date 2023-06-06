<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuManager;

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
        return app(SharpMenuManager::class)
            ->getFlattenedItems()
            ->first(fn (SharpMenuItem $menuItem) => $menuItem->isEntity())
            ?->getUrl();
    }
}
