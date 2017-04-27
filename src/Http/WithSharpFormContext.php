<?php

namespace Code16\Sharp\Http;

trait WithSharpFormContext
{

    function context(): SharpContext
    {
        return app(SharpContext::class);
    }
}