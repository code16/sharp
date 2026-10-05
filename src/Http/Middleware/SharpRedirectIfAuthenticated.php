<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class SharpRedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        $guard = $guard ?: sharp()->config()->get('auth.guard');

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

            return true;
        }

        return false;
    }
}
