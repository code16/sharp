<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Auth\AuthenticationException;
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

        if ($response->exception instanceof ValidationException
            || $response->exception instanceof AuthenticationException) {
            return $response;
        }

        if (isset($response->exception)) {
            return $this->mapException($response)->render($request) ?: $response;
        }

        return $response;
    }

    protected function mapException(Response|JsonResponse|RedirectResponse $response): SharpException
    {
        $exception = $response->exception;

        return match (true) {
            $exception instanceof SharpException => $exception,
            $exception instanceof HttpExceptionInterface => new SharpException(
                $exception->getMessage(),
                $response->getStatusCode(),
                $exception
            ),
            $exception instanceof ModelNotFoundException => new SharpException(
                $exception->getMessage(),
                404,
                $exception
            ),
            default => new SharpException(
                __('Server error'),
                500,
                $exception
            ),
        };
    }
}
