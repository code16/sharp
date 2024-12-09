<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuManager;

/**
 * @internal
 */
final class MenuData extends Data
{
    public function __construct(
        /** @var DataCollection<MenuItemData> */
        public DataCollection $items,
        public UserMenuData $userMenu,
        public bool $isVisible,
    ) {}

    public static function from(SharpMenuManager $menuManager): self
    {
        return new self(
            items: MenuItemData::collection(
                $menuManager
                    ->getItems()
                    ->map(fn (SharpMenuItem $item) => MenuItemData::from($item))
                    ->values()
            ),
            userMenu: new UserMenuData(
                MenuItemData::collection(
                    collect($menuManager->userMenu()?->getItems() ?? [])
                        ->map(fn (SharpMenuItem $item) => MenuItemData::from($item))
                )
            ),
            isVisible: $menuManager->menu()?->isVisible() ?? true,
        );
    }
}
