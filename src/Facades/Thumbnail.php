<?php

namespace Code16\Sharp\Facades;

use Illuminate\Support\Facades\Facade;

class Thumbnail extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'thumbnail';
    }
}
