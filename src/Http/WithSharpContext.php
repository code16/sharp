<?php

namespace Code16\Sharp\Http;

trait WithSharpContext
{

    /**
     * @return SharpContext
     */
    function context(): SharpContext
    {
        return app(SharpContext::class);
    }
}