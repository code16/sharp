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

        if ($key == 'auth.2fa.handler') {
            if (in_array(config('sharp.auth.2fa.handler'), ['notification', 'totp'])) {
                return app(config('sharp.auth.2fa.handler'));
            }

            return value(config('sharp.auth.2fa.handler'));
        }

        if ($key == 'auth.message_blade_path') {
            $blade = config('sharp.auth.message_blade_path');
            if ($blade && view()->exists($blade)) {
                return view($blade)->render();
            }

            return null;
        }

        return config('sharp.'.$key);
    }
}