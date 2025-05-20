<?php

namespace Code16\Sharp\Utils\Menu;

use Code16\Sharp\Utils\Entities\SharpEntityManager;

trait HasSharpMenuItems
{
    protected array $items = [];

    public function addEntityLink(string $entityKeyOrClassName, ?string $label = null, ?string $icon = null): self
    {
        if (class_exists($entityKeyOrClassName)) {
            $entityKeyOrClassName = app(SharpEntityManager::class)
                ->entityKeyFor($entityKeyOrClassName);
        }

        $this->items[] = (new SharpMenuItemLink($label, $icon))
            ->setEntity($entityKeyOrClassName);

        return $this;
    }

    public function addExternalLink(string $url, ?string $label = null, ?string $icon = null): self
    {
        $this->items[] = (new SharpMenuItemLink($label, $icon))->setUrl($url);

        return $this;
    }

    /**
     * @return SharpMenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
