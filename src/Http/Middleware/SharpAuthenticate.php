<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;

class SharpAuthenticate extends BaseAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);

            if ($checkHandler = config('sharp.auth.check_handler')) {
                throw_if(
                    ! instanciate($checkHandler)->check(auth()->guard($guards[0] ?? null)->user()),
                    new AuthenticationException()
                );
            }
        } catch (AuthenticationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthenticated user'], 401);
            }

            if ($loginPageUrl = value(config('sharp.auth.login_page_url'))) {
                return redirect()->guest($loginPageUrl);
            }

            if (app(SharpImpersonationHandler::class)->enabled()) {
                return redirect()->guest(route('code16.sharp.impersonate'));
            }

            return redirect()->guest(route('code16.sharp.login'));
        }

        return $next($request);
    }
}
