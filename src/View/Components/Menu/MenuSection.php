<?php

namespace Code16\Sharp\View\Components\Menu;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class MenuSection extends Component
{
    public function __construct(
        public SharpMenuItemSection $item,
        public ?string $currentEntityKey = null,
    ) {
    }

    public function hasCurrentItem(): bool
    {
        return $this->getItems()
            ->some(function (SharpMenuItem $item) {
                return $item->isEntity() && $item->getEntityKey() === $this->currentEntityKey;
            });
    }

    public function getItems(): Collection
    {
        return app(SharpMenuManager::class)->resolveSectionItems($this->item);
    }

    public function render()
    {
        return view('sharp::components.menu.menu-section', [
            'self' => $this,
        ]);
    }
}
