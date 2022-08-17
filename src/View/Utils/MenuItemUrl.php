<?php

namespace Code16\Sharp\View\Utils;

use Code16\Sharp\Utils\Menu\SharpMenuItem;

class MenuItemUrl extends MenuItem
{
    public string $type = 'url';
    public string $url;
    public ?string $icon;
    public string $key;
    public string $target = '_blank';

    public function __construct(SharpMenuItem $item)
    {
        $this->key = uniqid();
        $this->label = $item->getLabel();
        $this->icon = $item->getIcon();
        $this->url = $item->getUrl();
    }

    public function isValid(): bool
    {
        return true;
    }

    public function isMenuItemUrl(): bool
    {
        return true;
    }
}
