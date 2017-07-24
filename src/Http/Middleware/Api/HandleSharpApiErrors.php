<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Exceptions\Auth\SharpAuthenticationException;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Exceptions\EntityList\InvalidEntityStateException;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Exceptions\SharpException;

class HandleSharpApiErrors
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

        if($response->exception instanceof SharpException) {
            return response()->json([
                "message" => $response->exception->getMessage()
            ], $this->getHttpCodeFor($response->exception));
        }

        return $response;
    }

    private function getHttpCodeFor($exception)
    {
        if ($exception instanceof SharpApplicativeException) {
            // This is an applicative exception, we return it as a 417
            return 417;
        }

        if ($exception instanceof SharpAuthenticationException) {
            return 401;
        }

        if ($exception instanceof SharpAuthorizationException) {
            return 403;
        }

        if ($exception instanceof InvalidEntityStateException) {
            return 422;
        }

        return 500;
    }
}