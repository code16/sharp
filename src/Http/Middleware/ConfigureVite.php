<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Vite;

class ConfigureVite
{
    public function handle($request, Closure $next)
    {
        Vite::useBuildDirectory('vendor/sharp');

        if (Vite::hotFile() === public_path('/hot')) {
            Vite::useHotFile(public_path('vendor/sharp/hot')); // allow running "npm run dev" for symlinked assets
        }

        return $next($request);
    }
}
