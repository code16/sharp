<?php

namespace Code16\Sharp\EntityList\Commands;

enum CommandReturnAction: string
{
    case View = 'view';
    case Download = 'download';
    case Reload = 'reload';
    case Refresh = 'refresh';
    case Info = 'info';
    case StreamDownload = 'streamDownload';
    case Link = 'link';
}
