<?php

namespace Code16\Sharp\Auth\Passkeys;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;

/**
 * @implements PasskeyManager<Passkey>
 */
class SpatiePasskeyManager implements PasskeyManager
{
    public function isEnabled(): bool
    {
        return (bool) sharp()->config()->get('auth.passkeys.enabled');
    }

    public function setLastUsedPasskey(Model $passkey): void
    {
        Cookie::queue('sharp_last_used_passkey', $passkey->getKey(), 576000);
    }

    public function getLastUsedPasskey(): ?Passkey
    {
        return $this->model()::find(Cookie::get('sharp_last_used_passkey'));
    }

    public function getErrorMessage(): ?string
    {
        return session('authenticatePasskey::message');
    }

    public function setRedirectUrl(): void
    {
        $intendedUrl = Redirect::intended(
            route('code16.sharp.home', [
                'globalFilter' => sharp()->context()->globalFilterUrlSegmentValue(),
            ])
        )->getTargetUrl();

        config()->set(
            'passkeys.redirect_to_after_login',
            route('code16.sharp.passkeys.authenticated', [
                'intended_url' => $intendedUrl,
            ])
        );
    }

    public function userPasskeys(Authenticatable $user): Builder
    {
        return $user->passkeys();
    }

    public function userHasPasskey(Authenticatable $user): bool
    {
        return $user->passkeys()->exists();
    }

    public function model(): string
    {
        return Config::getPassKeyModel();
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(function (PasskeyUsedToAuthenticateEvent $event) {
            if ($event->request->headers->has('X-Sharp')) {
                $this->setLastUsedPasskey($event->passkey);
                $this->setRedirectUrl();
            }
        });
    }
}
