<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Inertia\Inertia;

class HomeController extends SharpProtectedController
{
    public function index(string $globalFilter)
    {
        if ($firstEntityUrl = $this->getFirstConfiguredEntityUrl()) {
            return redirect()->to($firstEntityUrl);
        }

        return Inertia::render('Welcome');
    }

    private function getFirstConfiguredEntityUrl(): ?string
    {
        return app(SharpMenuManager::class)
            ->getFlattenedItems()
            ->first(fn (SharpMenuItem $menuItem) => $menuItem->isEntity())
            ?->getUrl();
    }
}
