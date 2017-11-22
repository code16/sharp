<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;

class HandleSharpErrors
{

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        if($response->exception && $response->exception instanceof SharpAuthorizationException) {
            abort(403, $response->exception->getMessage());
        }

        return $response;
    }
}