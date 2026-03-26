<?php

namespace Code16\Sharp\Auth\Passkeys;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Cookie;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;

class PasskeyEventSubscriber
{
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(PasskeyUsedToAuthenticateEvent::class, function (PasskeyUsedToAuthenticateEvent $event) {
            Cookie::queue('sharp_last_used_passkey', $event->passkey->id, 576000);
        });
    }
}
