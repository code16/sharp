<?php

namespace Code16\Sharp\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class ConfigureZiggy
{
    public function handle(Request $request, Closure $next)
    {
        config()->set('ziggy', [
            'only' => ['code16.sharp.*'],
        ]);
        
        return $next($request);
    }
}
