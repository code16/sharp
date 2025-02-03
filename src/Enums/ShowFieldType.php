<?php

namespace Code16\Sharp\Enums;

enum ShowFieldType: string
{
    case File = 'file';
    case Html = 'html';
    case List = 'list';
    case Picture = 'picture';
    case Text = 'text';
    case EntityList = 'entityList';
}
