<?php

namespace Code16\Sharp\Utils;

class SharpTheme
{
    public function loginLogoUrl(): ?string
    {
        return file_exists(public_path($icon = 'sharp-assets/login-icon.png'))
            ? asset($icon).'?'.filemtime(public_path($icon))
            : config('sharp.theme.logo_urls.login');
    }
    
    public function menuLogoUrl(): ?string
    {
        return file_exists(public_path($icon = 'sharp-assets/menu-icon.png'))
            ? asset($icon).'?'.filemtime(public_path($icon))
            : config('sharp.theme.logo_urls.menu');
    }
}
