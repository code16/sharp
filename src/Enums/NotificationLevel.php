<?php

namespace Code16\Sharp\Enums;

enum NotificationLevel: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
}
