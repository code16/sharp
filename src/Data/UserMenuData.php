<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class UserMenuData extends Data
{
    public function __construct(
        /** @var MenuItemData[] */
        public array $items,
    ) {}
}
