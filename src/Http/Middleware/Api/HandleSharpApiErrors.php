<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Exceptions\EntityList\SharpInvalidEntityStateException;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

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

        if(isset($response->exception) && $response->exception) {
            if ($response->exception instanceof ValidationException) {
                return $this->handleValidationException($response);
            }

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

        if ($exception instanceof SharpAuthorizationException) {
            return 403;
        }

        if ($exception instanceof SharpInvalidEntityKeyException
            ||$exception instanceof ModelNotFoundException) {
            return 404;
        }

        if ($exception instanceof SharpInvalidEntityStateException) {
            return 422;
        }

        if ($exception instanceof SharpFormFieldValidationException) {
            return 500;
        }

        if(method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    /**
     * @param $response
     * @return JsonResponse
     */
    protected function handleValidationException($response)
    {
        return response()->json([
            "message" => $response->exception->getMessage(),
            "errors" => $response->exception->errors()
        ], 422);
    }
}