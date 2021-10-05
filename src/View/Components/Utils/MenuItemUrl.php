<?php

namespace Code16\Sharp\View\Components\Utils;

class MenuItemUrl extends MenuItem
{
    public string $type = "url";
    public string $url;
    public ?string $icon;
    public string $key;

    public function __construct(array $config)
    {
        $this->label = $config["label"] ?? "Unlabelled link";
        $this->icon = $config["icon"] ?? null;
        $this->url = $config["url"];
        $this->key = uniqid();
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
