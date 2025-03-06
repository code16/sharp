<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HandleSharpErrors
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->exception instanceof ValidationException) {
            return $response;
        }

        if (isset($response->exception)) {
            return $this->mapException($response)->render($request) ?: $response;
        }

        return $response;
    }

    protected function mapException(Response|JsonResponse|RedirectResponse $response): SharpException
    {
        if ($response->exception instanceof SharpException) {
            return $response->exception;
        }

        if ($response->exception instanceof HttpExceptionInterface) {
            return new SharpException(
                $response->exception->getMessage(),
                $response->getStatusCode(),
                $response->exception
            );
        }

        if ($response->exception instanceof ModelNotFoundException) {
            return new SharpException(
                $response->exception->getMessage(),
                404,
                $response->exception
            );
        }

        return new SharpException(
            __('Server error'),
            500,
            $response->exception
        );
    }
}
