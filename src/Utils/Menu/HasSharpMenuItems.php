<?php

namespace Code16\Sharp\Utils\Menu;

use Closure;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Links\SharpLinkTo;

trait HasSharpMenuItems
{
    protected array $items = [];

    public function addEntityLink(
        string $entityKeyOrClassName,
        ?string $label = null,
        ?string $icon = null,
        ?Closure $badge = null,
        ?string $badgeTooltip = null,
        string|SharpLinkTo|null $badgeLink = null,
    ): self {
        if (class_exists($entityKeyOrClassName)) {
            $entityKeyOrClassName = app(SharpEntityManager::class)
                ->entityKeyFor($entityKeyOrClassName);
        }

        $this->items[] = (new SharpMenuItemLink($label, $icon, $badge, $badgeTooltip, $badgeLink))
            ->setEntity($entityKeyOrClassName);

        return $this;
    }

    public function addExternalLink(
        string $url,
        ?string $label = null,
        ?string $icon = null,
        ?Closure $badge = null,
        ?string $badgeTooltip = null,
        string|SharpLinkTo|null $badgeLink = null,
    ): self {
        $this->items[] = (new SharpMenuItemLink($label, $icon, $badge, $badgeTooltip, $badgeLink))
            ->setUrl($url);

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
