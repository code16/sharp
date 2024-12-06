<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;

class SharpRedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->checkSharpUserAuthenticated($guard)) {
            return redirect(route('code16.sharp.home'));
        }

        return $next($request);
    }

    protected function checkSharpUserAuthenticated($guard)
    {
        if (auth()->guard($guard)->check()) {
            if ($checkHandler = config('sharp.auth.check_handler')) {
                return instanciate($checkHandler)
                    ->check(auth()->guard($guard)->user());
            }

            return true;
        }

        return false;
    }
}
