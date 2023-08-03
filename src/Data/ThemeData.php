<?php

namespace Code16\Sharp\Data;

use Code16\Sharp\Utils\SharpTheme;

final class ThemeData extends Data
{
    public function __construct(
        public ?string $loginLogoUrl,
        public ?string $menuLogoUrl,
    ) {
    }

    public static function from(SharpTheme $theme): self
    {
        return new self(
            loginLogoUrl: null,
//            loginLogoUrl: !auth()->check() ? $theme->loginLogoUrl() : null, // only if needed
            menuLogoUrl: $theme->menuLogoUrl(),
        );
    }
}
