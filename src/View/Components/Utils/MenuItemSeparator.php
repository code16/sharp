<?php

namespace Code16\Sharp\View\Components\Utils;

class MenuItemSeparator extends MenuItem
{
    public string $type = 'separator';
    public ?string $key = null;

    public function __construct(array $config)
    {
        $this->label = $config['label'] ?? null;
    }

    public function isValid(): bool
    {
        return true;
    }
}
