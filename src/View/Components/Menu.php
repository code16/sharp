<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\Utils\Menu\SharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuItemLink;
use Code16\Sharp\Utils\Menu\SharpMenuUserMenu;
use Code16\Sharp\View\Components\Menu\MenuSection;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Menu extends Component
{
    public string $title;
    public ?string $username;
    public ?string $currentEntityKey;
    public ?SharpMenuItemLink $currentEntityItem;
    public bool $hasGlobalFilters;
    private ?SharpMenu $sharpMenu = null;

    public function __construct()
    {
        $this->title = config('sharp.name', 'Sharp');
        $this->username = sharp_user()->{config('sharp.auth.display_attribute', 'name')};
        $this->currentEntityKey = currentSharpRequest()->breadcrumb()->first()->key ?? null;
        $this->currentEntityItem = $this->currentEntityKey
            ? $this->getEntityMenuItem($this->currentEntityKey)
            : null;
        $this->hasGlobalFilters = sizeof(value(config('sharp.global_filters')) ?? []) > 0;
    }

    public function render()
    {
        return view('sharp::components.menu', [
            'self' => $this,
        ]);
    }

    public function getItems(): Collection
    {
        return $this
            ->getSharpMenu()
            ?->items()
            ->filter(fn (SharpMenuItem $item) => $item->isSection()
                ? count((new MenuSection($item))->getItems()) > 0
                : $item->isAllowed()
            ) ?? collect();
    }

    public function getUserMenu(): ?SharpMenuUserMenu
    {
        return $this
            ->getSharpMenu()
            ?->userMenu();
    }

    public function getEntityMenuItem(string $entityKey): ?SharpMenuItemLink
    {
        return $this->getFlattenedItems()
            ->first(fn (SharpMenuItem $item) => $item->isEntity()
                && $item->getEntityKey() === $entityKey
            );
    }

    public function getFlattenedItems(): Collection
    {
        return $this->getItems()
            ->map(fn (SharpMenuItem $item) => $item->isSection()
                ? (new MenuSection($item))->getItems()
                : $item
            )
            ->flatten();
    }

    private function getSharpMenu(): ?SharpMenu
    {
        if ($this->sharpMenu === null) {
            if (($sharpMenu = config('sharp.menu')) === null) {
                return null;
            }

            $this->sharpMenu = is_string($sharpMenu)
                ? app($sharpMenu)
                : $sharpMenu;

            $this->sharpMenu->build();
        }

        return $this->sharpMenu;
    }
}
