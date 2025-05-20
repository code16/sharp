<?php

namespace Code16\Sharp\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SharpAuthenticationException extends AuthenticationException
{
    public function __construct($message = 'Unauthenticated.', array $guards = [], $redirectTo = null)
    {
        parent::__construct($message, $guards, $redirectTo);
    }

    public function render(Request $request): SymfonyResponse
    {
        if ($request->routeIs('code16.sharp.api.*')) {
            return response()->json([
                'message' => $this->getMessage(),
            ], 401);
        }

        return $request->inertia()
            ? Inertia::location($request->fullUrl()) // if request is Inertia, full reload current page to allow external url redirection
            : redirect()->guest($this->redirectTo($request));
    }
}
