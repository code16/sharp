<?php

namespace Code16\Sharp\Config;

class SharpLegacyConfigBuilder extends SharpConfigBuilder
{
    public function get(string $key): mixed
    {
        return match ($key) {
            'entity_resolver' => is_string(config('sharp.entities'))
                ? config('sharp.entities')
                : null,
            'theme.logo_url' => config('sharp.theme.logo_url', config('sharp.theme.logo_urls.menu')),
            'auth.impersonate.handler' => value(config('sharp.auth.impersonate.handler')),
            'auth.forgotten_password.password_broker' => value(config('sharp.auth.forgotten_password.password_broker')),
            'auth.2fa.handler' => $this->get2faHandler(),
            'auth.message_blade_path' => $this->getMessageBladePath(),
            default => config('sharp.'.$key),
        };
    }

    private function get2faHandler(): mixed
    {
        $handler = config('sharp.auth.2fa.handler');

        if (in_array($handler, ['notification', 'totp'])) {
            return app($handler);
        }

        return value($handler);
    }

    private function getMessageBladePath(): ?string
    {
        $blade = config('sharp.auth.message_blade_path');

        if ($blade && view()->exists($blade)) {
            return view($blade)->render();
        }

        return null;
    }
}
