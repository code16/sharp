<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class SharpAuthenticate extends BaseAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if ($checkHandler = config('sharp.auth.check_handler')) {
            if(! instanciate($checkHandler)->check(auth()->guard($guards[0] ?? null)->user())) {
                $this->unauthenticated($request, $guards);
            }
        }

        return $next($request);
    }

    protected function redirectTo(Request $request)
    {
        if ($loginPageUrl = value(config('sharp.auth.login_page_url'))) {
            return $loginPageUrl;
        }

        if (app(SharpImpersonationHandler::class)->enabled()) {
            return route('code16.sharp.impersonate');
        }

        return route('code16.sharp.login');
    }
}
