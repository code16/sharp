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

        if ($key == 'auth.impersonate.handler') {
            return value(config('sharp.auth.impersonate.handler'));
        }

        if ($key == 'auth.forgotten_password.password_broker') {
            return value(config('sharp.auth.forgotten_password.password_broker'));
        }

        return config('sharp.' . $key);
    }
}