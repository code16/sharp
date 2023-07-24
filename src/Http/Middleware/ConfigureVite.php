<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Vite;

class ConfigureVite
{
    public function handle($request, Closure $next)
    {
        Vite::useBuildDirectory('vendor/sharp')
            ->useHotFile(public_path('vendor/sharp/hot'));

        return $next($request);
    }
}
