<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Code16\Sharp\Enums\MultiFactorMethod;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class MultiFactorManager
{
    public function isEnabled(): bool
    {
        return sharp()->config()->get('auth.2fa.enabled');
    }

    public function currentMethod(): ?MultiFactorMethod
    {
        return $this->currentHandler()?->method();
    }

    public function currentHandler(): ?Sharp2faHandler
    {
        return app(Sharp2faHandler::class);
    }

    public function currentHandlerHelpText(): ?string
    {
        return method_exists($this->currentHandler(), 'formHelpText')
            ? $this->currentHandler()->setUser($this->pendingUser())->formHelpText()
            : trans('sharp::auth.2fa.form_help_text');
    }

    public function isUsingPasskeyMethod(): bool
    {
        return $this->isEnabled()
            && $this->currentMethod() === MultiFactorMethod::Passkey
            && $this->currentHandler()->isExpectingLogin();
    }

    public function pendingUser(): ?Authenticatable
    {
        if ($userId = $this->currentHandler()?->userId()) {
            return $this->findUser($userId);
        }

        return null;
    }

    protected function findUser($id): ?Authenticatable
    {
        return once(fn () => Auth::guard(sharp()->config()->get('auth.guard'))
            ->getProvider()
            ->retrieveById($id)
        );
    }
}
