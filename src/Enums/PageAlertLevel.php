<?php

namespace Code16\Sharp\Enums;

enum PageAlertLevel: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Danger = 'danger';
    case Primary = 'primary';
    case Secondary = 'secondary';
}
