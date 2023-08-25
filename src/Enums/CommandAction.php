<?php

namespace Code16\Sharp\Enums;

enum CommandAction: string
{
    case Download = 'download';
    case Info = 'info';
    case Link = 'link';
    case Reload = 'reload';
    case Refresh = 'refresh';
    case Step = 'step';
    case StreamDownload = 'streamDownload';
    case View = 'view';
}
