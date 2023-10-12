<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class HandleSharpApiErrors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (isset($response->exception) && $response->exception) {
            if ($response->exception instanceof ValidationException) {
                return $this->handleValidationException($response);
            }

            $code = $this->getHttpCodeFor($response->exception);

            if ($code != 500) {
                return response()->json(
                    ['message' => $response->exception->getMessage()],
                    $code,
                );
            }

            // Let Laravel regular ErrorHandler manage the error
        }

        return $response;
    }

    private function getHttpCodeFor($exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return 404;
        }

        if ($exception instanceof SharpException || method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    protected function handleValidationException($response): JsonResponse
    {
        return response()->json([
            'message' => $response->exception->getMessage(),
            'errors' => $response->exception->errors(),
        ], 422);
    }
}
