<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HandleSharpErrors
{
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var \Illuminate\Http\Response $response
         */
        $response = $next($request);

        if($response->exception instanceof ValidationException) {
            return $response;
        }

        if(isset($response->exception) && !($response->exception instanceof SharpException)) {
            return (
                new SharpException(
                    $response->exception->getMessage(),
                    match(true) {
                        $response->exception instanceof ModelNotFoundException => 404,
                        default => 500
                    },
                    $response->exception
                )
            )->render($request);
        }

        return $response;
    }
}
