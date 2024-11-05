<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Utils\Menu\SharpMenuItemLink;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Menu extends Component
{
    public string $title;
    public ?string $currentEntityKey;
    public ?SharpMenuItemLink $currentEntityItem;
    public bool $hasGlobalFilters;
    public bool $isVisible = true;

    public function __construct(private SharpMenuManager $menuManager)
    {
        $this->title = config('sharp.name', 'Sharp');
        $this->currentEntityKey = currentSharpRequest()->breadcrumb()->first()->key ?? null;
        $this->currentEntityItem = $this->currentEntityKey
            ? $this->menuManager->getEntityMenuItem($this->currentEntityKey)
            : null;
        $this->hasGlobalFilters = count(value(config('sharp.global_filters')) ?? []) > 0;
        $this->isVisible = $this->menuManager->menu()?->isVisible() ?? true;
    }

    public function getItems(): Collection
    {
        return $this->menuManager->getItems();
    }

    public function render()
    {
        return view('sharp::components.menu', [
            'self' => $this,
        ]);
    }
}
