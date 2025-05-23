<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Utils\Icons\IconManager;
use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuItemLink;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;
use Code16\Sharp\Utils\Menu\SharpMenuItemSeparator;
use Code16\Sharp\Utils\Menu\SharpMenuManager;

/**
 * @internal
 */
final class MenuItemData extends Data
{
    public function __construct(
        public ?IconData $icon = null,
        public ?string $label = null,
        public ?string $badge = null,
        public ?string $badgeTooltip = null,
        public ?string $badgeUrl = null,
        public ?string $url = null,
        public bool $isExternalLink = false,
        public ?string $entityKey = null,
        public bool $isSeparator = false,
        public bool $current = false,
        /** @var MenuItemData[]|null */
        public ?array $children = null,
        public bool $isCollapsible = false,
    ) {}

    public static function from(SharpMenuItem $item)
    {
        if ($item instanceof SharpMenuItemSection) {
            return new self(
                label: $item->getLabel(),
                children: self::collection(
                    app(SharpMenuManager::class)
                        ->resolveSectionItems($item)
                        ->map(fn (SharpMenuItem $item) => self::from($item))
                        ->values()
                ),
                isCollapsible: $item->isCollapsible(),
            );
        }

        if ($item instanceof SharpMenuItemSeparator) {
            return new self(
                label: $item->getLabel(),
                isSeparator: true,
            );
        }

        if ($item instanceof SharpMenuItemLink) {
            return new self(
                icon: IconData::optional(app(IconManager::class)->iconToArray($item->getIcon())),
                label: $item->getLabel(),
                badge: $item->getBadge(),
                badgeTooltip: $item->getBadgeTooltip(),
                badgeUrl: $item->getBadgeUrl(),
                url: $item->getUrl(),
                isExternalLink: $item->isExternalLink(),
                entityKey: $item->isEntity() ? $item->getEntityKey() : null,
                current: $item->isEntity() && $item->isCurrent(),
            );
        }

        return new self();
    }
}
