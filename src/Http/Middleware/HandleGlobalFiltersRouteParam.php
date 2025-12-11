<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class HandleGlobalFiltersRouteParam
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && ($filterKey = $request->route('filterKey'))) {
            URL::defaults(['filterKey' => $filterKey]);
        }

        return $next($request);
    }
}
