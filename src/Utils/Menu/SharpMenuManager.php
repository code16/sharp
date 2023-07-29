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
            ->filter(function (SharpMenuItem $item) {
                if ($item->isSection()) {
                    return count($this->resolveSectionItems($item)) > 0;
                }

                return $item->isAllowed();
            }) ?? collect();
    }

    public function getFlattenedItems(): Collection
    {
        return $this->getItems()
            ->map(function (SharpMenuItem $item) {
                return $item->isSection()
                    ? $this->resolveSectionItems($item)
                    : $item;
            })
            ->flatten();
    }

    public function getEntityMenuItem(string $entityKey): ?SharpMenuItemLink
    {
        return $this->getFlattenedItems()
            ->first(function (SharpMenuItem $item) use ($entityKey) {
                return $item->isEntity()
                    && $item->getEntityKey() === $entityKey;
            });
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
        if (($sharpMenu = config('sharp.menu')) === null) {
            $this->menu = null;

            return;
        }

        $this->menu = is_string($sharpMenu)
            ? app($sharpMenu)
            : $sharpMenu;

        $this->menu->build();
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
}
