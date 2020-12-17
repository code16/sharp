<?php

namespace Code16\Sharp\Http;

trait WithSharpContext
{

    function context(): SharpContext
    {
        return app(SharpContext::class);
    }
}