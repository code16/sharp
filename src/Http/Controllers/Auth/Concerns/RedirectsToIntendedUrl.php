<?php

namespace Code16\Sharp\Http\Controllers\Auth\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait RedirectsToIntendedUrl
{
    /**
     * Inertia follows redirects in XHR: when the intended URL is not a Sharp page
     * (e.g. an app route using the sharp_auth middleware), force a full page visit.
     */
    protected function redirectToIntendedUrl(?string $url = null): SymfonyResponse
    {
        $redirect = $url
            ? redirect()->to($url)
            : redirect()->intended(route('code16.sharp.home'));

        if (request()->inertia() && ! $this->isSharpUrl($redirect->getTargetUrl())) {
            return Inertia::location($redirect->getTargetUrl());
        }

        return $redirect;
    }

    private function isSharpUrl(string $url): bool
    {
        try {
            return Route::getRoutes()->match(Request::create($url))->named('code16.sharp.*');
        } catch (NotFoundHttpException|MethodNotAllowedHttpException) {
            return false;
        }
    }
}
