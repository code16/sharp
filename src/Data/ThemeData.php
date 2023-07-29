<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Utils\SharpTheme;

class ThemeData extends Data
{
    public function __construct(
        public ?string $menuLogoUrl
    ) {
    }
    
    public static function from(SharpTheme $theme)
    {
        return new self(
            menuLogoUrl: $theme->menuLogoUrl(),
        );
    }
}
