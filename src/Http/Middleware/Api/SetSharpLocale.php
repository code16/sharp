<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Carbon\Carbon;
use Closure;

class SetSharpLocale
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (config('sharp.locale')) {
            setlocale(LC_ALL, config('sharp.locale'));
            Carbon::setLocale(config('sharp.locale'));
        }

        return $next($request);
    }
}
