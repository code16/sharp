<?php

namespace Code16\Sharp\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
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

    public function render(Request $request): Response|RedirectResponse|JsonResponse|bool
    {
        if ($this->getStatusCode() >= 500 && config('app.debug')) {
            return false;
        }

        if ($request->routeIs('code16.sharp.api.*')) {
            return false;
        }

        return Inertia::render('Error', [
            'status' => $this->getStatusCode(),
            'message' => $this->getMessage(),
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
