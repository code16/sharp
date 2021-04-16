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
        
        $this->entities = collect($this->entities)
            ->reverse()
            ->skipWhile(fn ($entity) => $entity->type === 'separator')
            ->reverse()
            ->toArray();
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
