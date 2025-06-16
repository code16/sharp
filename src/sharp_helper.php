<?php

function sharp(): \Code16\Sharp\Utils\SharpUtil
{
    return app(\Code16\Sharp\Utils\SharpUtil::class);
}

/**
 * @deprecated use sharp()->context() instead
 */
function currentSharpRequest(): \Code16\Sharp\Http\Context\CurrentSharpRequest
{
    return app(\Code16\Sharp\Http\Context\CurrentSharpRequest::class);
}

function instanciate($class)
{
    return is_string($class) ? app($class) : value($class);
}
