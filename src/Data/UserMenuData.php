<?php

namespace Code16\Sharp\Data;

final class UserMenuData extends Data
{
    public function __construct(
        /** @var DataCollection<MenuItemData> */
        public DataCollection $items,
    ) {
    }
}