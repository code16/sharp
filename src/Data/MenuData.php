<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuManager;

class MenuData extends Data
{
    public function __construct(
        /** @var DataCollection<MenuItemData> */
        public DataCollection $items,
        public UserMenuData $userMenu,
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
            userMenu: new UserMenuData(
                MenuItemData::collection(
                    collect($menuManager->userMenu()->getItems())
                        ->map(fn (SharpMenuItem $item) => MenuItemData::from($item))
                )
            ),
        );
    }
}
