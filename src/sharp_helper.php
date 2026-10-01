<?php

use Code16\Sharp\Utils\SharpUtil;

function sharp(): SharpUtil
{
    return app(SharpUtil::class);
}

function instanciate($class)
{
    return is_string($class) ? app($class) : value($class);
}
