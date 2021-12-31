<?php

namespace Code16\Sharp\Utils\Menu;

trait HasMenuEntityLinks
{
    protected array $items = [];
    
    public function addEntityLink(string $entityKey, ?string $label = null, ?string $icon = null): self
    {
        $this->items[] = new SharpMenuItem($entityKey, $label, $icon);
        return $this;
    }

    public function getEntityLinks(): array
    {
        return $this->items;
    }
}