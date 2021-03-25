<?php

namespace Code16\Sharp\View\Components\Utils;

class MenuItemEntity extends MenuItem
{
    public string $type = "entity";
    public string $key;
    public ?string $icon;
    public string $url;

    public function __construct(array $config)
    {
        $this->key = $config['entity'];
        $this->label = $config["label"] ?? "Unnamed entity";
        $this->icon = $config["icon"] ?? null;
        $this->url = $config["single"] ?? false
                ? route('code16.sharp.single-show', ["entityKey" => $this->key])
                : route('code16.sharp.list', $this->key);
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
