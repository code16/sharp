<?php

namespace Code16\Sharp\Exceptions;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class SharpException extends \Exception
{
    public function __construct(
        string $message = '',
        protected int $statusCode = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function render(Request $request): SymfonyResponse|bool
    {
        if ($this->getStatusCode() >= 500 && config('app.debug')) {
            // for debugging purposes, redirect to the errored page
            if ($request->inertia() && $request->method() === 'GET') {
                return Inertia::location($request->fullUrl());
            }

            return false;
        }

        if ($request->routeIs('code16.sharp.api.*', 'code16.sharp.download.show')) {
            return false;
        }

        if (! app()->isBooted()) {
            throw $this;
        }

        return Inertia::render(
            'Error',
            [
                'status' => $this->getStatusCode(),
                'message' => $this->getMessage(),
                'previous' => $this->getPrevious() && $this->getPrevious()->getMessage() !== $this->getMessage() ? [
                    'message' => $this->getPrevious()->getMessage(),
                ] : null,
            ])
            ->toResponse($request)
            ->setStatusCode($this->getStatusCode());
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
