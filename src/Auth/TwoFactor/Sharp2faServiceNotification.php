<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class Sharp2faServiceNotification implements Sharp2faService
{
    public function generateAndSendTokenFor($user): void
    {
        $code = random_int(100000, 999999);
        Session::put('sharp:2fa:code', Hash::make($code));
        
        $notificationClass = config('sharp.auth.2fa.notification_class', Sharp2faDefaultNotification::class);
        $user->notify(new $notificationClass($code));
    }
}