<?php

namespace Code16\Sharp\View\Components\Utils;

class MenuItemDashboard extends MenuItem
{
    public string $type = "dashboard";
    public string $key;
    public ?string $icon;
    public string $url;

    public function __construct(array $config)
    {
        if (!sharp_has_ability("view", $config['dashboard'])) {
            return;
        }

        $this->key = $config['dashboard'];
        $this->label = $config["label"] ?? "Unnamed dashboard";
        $this->icon = $config["icon"] ?? null;
        $this->url = route('code16.sharp.dashboard', $this->key);
    }

    public function isValid(): bool
    {
        return !is_null($this->key);
    }

    public function isMenuItemDashboard(): bool
    {
        return true;
    }
}