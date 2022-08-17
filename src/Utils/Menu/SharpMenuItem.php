<?php

namespace Code16\Sharp\Utils\Menu;

class SharpMenuItem
{
    public function isSection(): bool
    {
        return false;
    }

    public function isSeparator(): bool
    {
        return false;
    }

    public function isDashboardEntity(): bool
    {
        return false;
    }

    public function isEntity(): bool
    {
        return false;
    }

    public function isExternalLink(): bool
    {
        return false;
    }
}
