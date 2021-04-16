<?php

namespace Code16\Sharp\View\Components\Utils;

class MenuItemCategory extends MenuItem
{
    public string $type = "category";
    public array $entities = [];

    public function __construct(array $category)
    {
        $this->label = $category["label"] ?? "Unnamed category";

        foreach ((array)($category["entities"] ?? []) as $entityConfig) {
            if($menuEntity = static::parse($entityConfig)) {
                $this->entities[] = $menuEntity;
            }
        }
        
        $this->entities = $this->sanitizeItems($this->entities);
    }

    public function sanitizeItems($items): array
    {
        $filtered = [];
        
        foreach (array_reverse($items) as $key => $item) {
            if($item->type == 'separator') {
                if(count($filtered) == 0 || end($filtered)->type == 'separator') {
                    continue;
                }
            }
            $filtered[] = $item;
        }
        
        return array_reverse($filtered);
    }
    
    public function isValid(): bool
    {
        return count($this->entities) != 0;
    }

    public function isMenuItemCategory(): bool
    {
        return true;
    }
}
