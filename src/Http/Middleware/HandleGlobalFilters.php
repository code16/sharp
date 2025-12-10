<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

class HandleGlobalFilters
{
    public function handle($request, Closure $next)
    {
        URL::defaults(['filterKey' => sharp()->context()->globalFilterUrlSegmentValue()]);

        return $next($request);
    }
}
