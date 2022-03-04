<?php

namespace Code16\Sharp\View\Components\Utils;

class MenuItemDashboard extends MenuItem
{
    public string $type = 'dashboard';
    public string $key;
    public ?string $icon;
    public string $url;

    public function __construct(array $config)
    {
        $this->key = $config['dashboard'];
        $this->label = $config['label'] ?? 'Unnamed dashboard';
        $this->icon = $config['icon'] ?? null;
        $this->url = route('code16.sharp.dashboard', $this->key);
    }

    public function isValid(): bool
    {
        return sharp_has_ability('view', $this->key);
    }

    public function isMenuItemDashboard(): bool
    {
        return true;
    }
}
