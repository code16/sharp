<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;

class SharpRedirectIfAuthenticated
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth()->guard($guard)->check()) {
            return redirect(route("code16.sharp.dashboard"));
        }

        return $next($request);
    }
}