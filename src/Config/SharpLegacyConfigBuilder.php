<?php

namespace Code16\Sharp\Config;

class SharpLegacyConfigBuilder extends SharpConfigBuilder
{
    public function get(string $key): mixed
    {
        if ($key == 'entity_resolver') {
            return is_string(config('sharp.entities'))
                ? config('sharp.entities')
                : null;
        }

        if ($key == 'theme.logo_url') {
            return config('sharp.theme.logo_url', config('sharp.theme.logo_urls.menu'));
        }

        return config('sharp.' . $key);
    }
}