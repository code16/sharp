<?php

namespace Code16\Sharp\View\Utils;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;

class MenuItemSection extends MenuItem
{
    public string $type = 'category';
    public array $entities = [];
    public bool $collapsible = true;

    public function __construct(SharpMenuItemSection $section)
    {
        $this->label = $section->getLabel();
        $this->entities = collect($section->getItems())
            ->map(function (SharpMenuItem $entityMenuItem) {
                return MenuItem::buildFromItemClass($entityMenuItem);
            })
            ->filter()
            ->toArray();
        $this->collapsible = $section->isCollapsible();

        $this->sanitizeItems();
    }

    private function sanitizeItems(): void
    {
        $filtered = [];

        foreach (array_reverse($this->entities) as $item) {
            if ($item->type === 'separator') {
                // Prevent separators in last position or stacked
                if (count($filtered) === 0 || end($filtered)->type === 'separator') {
                    continue;
                }
            }
            $filtered[] = $item;
        }

        $this->entities = array_reverse($filtered);
    }

    public function isValid(): bool
    {
        return count($this->entities) != 0;
    }

    public function isMenuItemSection(): bool
    {
        return true;
    }
}
