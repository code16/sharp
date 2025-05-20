<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

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
            if (Gate::has('viewSharp')) {
                return Gate::allows('viewSharp');
            }

            // Legacy check:
            if ($checkHandler = config('sharp.auth.check_handler')) {
                return instanciate($checkHandler)
                    ->check(auth()->guard($guard)->user());
            }

            return true;
        }

        return false;
    }
}
