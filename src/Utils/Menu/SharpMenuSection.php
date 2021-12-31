<?php

namespace Code16\Sharp\Utils\Menu;

class SharpMenuSection extends SharpMenuItem
{
    use HasMenuEntityLinks;
    
    public function __construct(string $label)
    {
        parent::__construct(null, $label, null);
    }

    public function isSection(): bool
    {
        return true;
    }

    public function isEntity(): bool
    {
        return false;
    }
}