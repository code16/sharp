<?php

namespace Code16\Sharp\Enums;

enum MultiFactorMethod: string
{
    case Notification = 'notification';
    case Totp = 'totp';
    case Passkey = 'passkey';
}
