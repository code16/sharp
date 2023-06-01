<?php

namespace Code16\Sharp\View\Components;


use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuItemLink;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Code16\Sharp\View\Components\Menu\MenuSection;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Menu extends Component
{
    public string $title;
    public ?string $currentEntityKey;
    public ?SharpMenuItemLink $currentEntityItem;
    public bool $hasGlobalFilters;

    public function __construct() {
        $this->title = config('sharp.name', 'Sharp');
        $this->currentEntityKey = currentSharpRequest()->breadcrumb()->first()->key ?? null;
        $this->currentEntityItem = $this->currentEntityKey
            ? app(SharpMenuManager::class)->getEntityMenuItem($this->currentEntityKey)
            : null;
        $this->hasGlobalFilters = sizeof(value(config('sharp.global_filters')) ?? []) > 0;
    }

    public function getItems(): Collection
    {
        return app(SharpMenuManager::class)->getItems();
    }

    public function render()
    {
        return view('sharp::components.menu', [
            'self' => $this,
        ]);
    }
}
