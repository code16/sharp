<?php

namespace Code16\Sharp\View\Components\Utils;

use Code16\Sharp\Utils\Menu\SharpMenuItem;

class MenuItemEntity extends MenuItem
{
    public string $type;
    public string $key;
    public ?string $icon;
    public string $url;

    public function __construct(SharpMenuItem $item)
    {
        $this->type = $item->isDashboardEntity() ? "dashboard" : "entity";
        $this->key = $item->getKey();
        $this->label = $item->getLabel();
        $this->icon = $item->getIcon();
        $this->url = $item->getUrl();
    }

    public function isValid(): bool
    {
        return sharp_has_ability("entity", $this->key);
    }

    public function isMenuItemEntity(): bool
    {
        return true;
    }
}
