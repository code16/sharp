<?php

namespace Code16\Sharp\Auth\Passkeys;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;

/**
 * @internal
 *
 * @template TModel of Model
 */
interface PasskeyManager
{
    public function isEnabled(): bool;

    /**
     * @param  TModel  $passkey
     */
    public function setLastUsedPasskey(Model $passkey): void;

    /**
     * @return TModel|null
     */
    public function getLastUsedPasskey(): ?Model;

    public function getErrorMessage(): ?string;

    public function setRedirectUrl(): void;

    public function userPasskeys(Authenticatable $user): Builder;

    public function userHasPasskey(Authenticatable $user): bool;

    /**
     * @return class-string<TModel>
     */
    public function model(): string;

    public function subscribe(Dispatcher $events): void;
}
