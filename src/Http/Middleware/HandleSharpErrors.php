<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Form\SharpFormException;

class HandleSharpErrors
{

    /**
     * Return http 417 on SharpFormException.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        if($response->exception && $response->exception instanceof SharpFormException) {
            return response()->json([
                "message" => $response->exception->getMessage()
            ], 417);
        }

        return $response;
    }
}