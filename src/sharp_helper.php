<?php

function sharp(): \Code16\Sharp\Utils\SharpUtil
{
    return app(\Code16\Sharp\Utils\SharpUtil::class);
}

function instanciate($class)
{
    return is_string($class) ? app($class) : value($class);
}
