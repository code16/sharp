<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;

class SetSharpLocale
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (config('sharp.locale')) {
            setlocale(LC_ALL, config('sharp.locale'));
        }

        return $next($request);
    }
}
