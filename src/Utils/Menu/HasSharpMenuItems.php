<?php

namespace Code16\Sharp\Utils\Menu;

trait HasSharpMenuItems
{
    protected array $items = [];
    
    public function addEntityLink(string $entityKey, ?string $label = null, ?string $icon = null): self
    {
        $this->items[] = (new SharpMenuItemLink($label, $icon))->setEntity($entityKey);
        return $this;
    }

    public function addExternalLink(string $url, ?string $label = null, ?string $icon = null): self
    {
        $this->items[] = (new SharpMenuItemLink($label, $icon))->setUrl($url);
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}