<?php

namespace Code16\Sharp\Exceptions;

use Illuminate\Http\JsonResponse;
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

    public function render(Request $request): Response|JsonResponse|bool
    {
        if(app()->environment('local')) {
            return false;
        }

        if($request->expectsJson() && !$request->inertia()) {
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
}
