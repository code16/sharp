<?php

namespace Code16\Sharp\Exceptions;

use Code16\Sharp\Enums\SessionStatusLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

class SharpTokenMismatchException extends \Exception
{
    public function __construct(TokenMismatchException $previous)
    {
        parent::__construct($previous->getMessage(), 0, $previous);
    }

    public function render(Request $request): RedirectResponse|JsonResponse
    {
        session()->flash('status', __('sharp::errors.419.status_displayed_in_login_page'));
        session()->flash('status_level', SessionStatusLevel::Error->value);

        if ($request->routeIs('code16.sharp.api.*')) {
            // redirect on the front side
            return response()->json([
                'message' => $this->getMessage(),
            ], 419);
        }

        return redirect()->back();
    }

    public function getStatusCode(): int
    {
        return 419;
    }
}
