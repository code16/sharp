<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;

class InvalidateCache
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        return $response->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT')
            ->header('Cache-Control', 'no-cache, must-revalidate, no-store, max-age=0, private');
    }
}
