<?php

namespace Code16\Sharp\Auth\Impersonate;

abstract class SharpImpersonationHandler
{
    public function enabled(): bool
    {
        return sharp()->config()->get('auth.impersonate.enabled') === true
            && app()->isLocal();
    }

    abstract public function getUsers(): array;
}
