<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuManager;

class MenuData extends Data
{
    public function __construct(
        /** @var DataCollection<MenuItemData> */
        public DataCollection $items,
        public ?string $logo,
        public bool $hasGlobalFilters
    ) {
    }

    public static function from(SharpMenuManager $menuManager): self
    {
        return new static(
            items: MenuItemData::collection(
                $menuManager
                    ->getItems()
                    ->map(fn (SharpMenuItem $item) => MenuItemData::from($item))
            ),
            logo: file_exists(public_path($icon = 'sharp-assets/menu-icon.png'))
                ? asset($icon).filemtime(public_path($icon))
                : config('sharp.theme.logo_urls.menu'),
            hasGlobalFilters: count(value(config('sharp.global_filters')) ?? []) > 0
        );
    }
}
