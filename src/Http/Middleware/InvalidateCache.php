<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class InvalidateCache
{
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        $response->headers->set('Cache-Control', 'no-cache, must-revalidate, no-store, max-age=0, private');

        return $response;
    }
}
