<?php

namespace Code16\Sharp\Enums;

enum FormFieldType: string
{
    case Autocomplete = 'autocomplete';
    case Check = 'check';
    case Date = 'date';
    case DateRange = 'daterange';
    case Editor = 'editor';
    case Geolocation = 'geolocation';
    case Html = 'html';
    case List = 'list';
    case Number = 'number';
    case Select = 'select';
    case Tags = 'tags';
    case Text = 'text';
    case Textarea = 'textarea';
    case Upload = 'upload';
}
