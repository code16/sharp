<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuManager;

final class MenuItemData extends Data
{
    public function __construct(
        public ?string $icon = null,
        public ?string $label = null,
        public ?string $url = null,
        public bool $isExternalLink = false,
        public ?string $entityKey = null,
        public bool $isSeparator = false,
        public bool $current = false,

        /** @var DataCollection<MenuItemData> */
        public ?DataCollection $children = null,
        public bool $isCollapsible = false,
    ) {
    }

    public static function from(SharpMenuItem $item)
    {
        if ($item->isSection()) {
            return new self(
                label: $item->getLabel(),
                children: self::collection(
                    app(SharpMenuManager::class)
                        ->resolveSectionItems($item)
                        ->map(fn (SharpMenuItem $item) => self::from($item))
                ),
                isCollapsible: $item->isCollapsible(),
            );
        }

        if ($item->isSeparator()) {
            return new self(
                label: $item->getLabel(),
                isSeparator: true,
            );
        }

        return new self(
            icon: $item->getIcon(),
            label: $item->getLabel(),
            url: $item->getUrl(),
            isExternalLink: $item->isExternalLink(),
            entityKey: $item->isEntity() ? $item->getEntityKey() : null,
            current: $item->isEntity() && $item->isCurrent(),
        );
    }
}
