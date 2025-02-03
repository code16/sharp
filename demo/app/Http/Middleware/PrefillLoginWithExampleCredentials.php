<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PrefillLoginWithExampleCredentials
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('code16.sharp.login')) {
            Inertia::share([
                'prefill' => [
                    'login' => 'admin@example.org',
                    'password' => 'password',
                ],
            ]);
        }

        return $next($request);
    }
}
