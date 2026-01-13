<?php

namespace Code16\Sharp\Utils\Menu;

use Illuminate\Support\Collection;

class SharpMenuManager
{
    protected ?SharpMenu $menu = null;

    public function menu(): ?SharpMenu
    {
        if ($this->menu === null) {
            $this->buildMenu();
        }

        return $this->menu;
    }

    public function userMenu(): ?SharpMenuUserMenu
    {
        return $this->menu()?->userMenu();
    }

    public function getItems(): Collection
    {
        return $this->menu()
            ?->items()
            ->filter(fn (SharpMenuItem $item) => $item->isSection()
                ? count($this->resolveSectionItems($item)) > 0
                : $item->isAllowed()
            ) ?? collect();
    }

    public function getFlattenedItems(): Collection
    {
        return $this->getItems()
            ->map(fn (SharpMenuItem $item) => $item->isSection()
                ? $this->resolveSectionItems($item)
                : $item
            )
            ->merge($this->userMenu()?->getItems() ?? collect())
            ->flatten();
    }

    public function getEntityMenuItem(string $entityKey): ?SharpMenuItemLink
    {
        return $this->getFlattenedItems()
            ->first(fn (SharpMenuItem $item) => $item->isEntity() && $item->getEntityKey() === $entityKey);
    }

    public function resolveSectionItems(SharpMenuItemSection $section): Collection
    {
        return collect($section->getItems())
            ->filter(fn (SharpMenuItem $item) => $item->isAllowed())
            ->pipe(fn ($items) => $this->filterSeparators($items))
            ->values();
    }

    protected function buildMenu(): void
    {
        $this->menu = instanciate(sharp()->config()->get('menu'));
        $this->menu?->build();
    }

    private function filterSeparators(Collection $items): Collection
    {
        $filtered = collect();

        foreach ($items->reverse() as $item) {
            if ($item->isSeparator()) {
                // Prevent separators in last position or stacked
                if (count($filtered) === 0 || $filtered->last()?->isSeparator()) {
                    continue;
                }
            }
            $filtered->push($item);
        }

        return $filtered->reverse();
    }

    public function reset(): void
    {
        if ($this->menu) {
            $this->menu->reset();
            $this->buildMenu();
        }
    }
}
