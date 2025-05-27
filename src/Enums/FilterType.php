<?php

namespace Code16\Sharp\Enums;

enum FilterType: string
{
    case Autocomplete = 'autocomplete';
    case Select = 'select';
    case DateRange = 'daterange';
    case Check = 'check';
}
