<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class SharpAuthenticate extends BaseAuthenticate
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        parent::__construct($auth);
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if ($checkHandler = config('sharp.auth.check_handler')) {
            if (! app($checkHandler)->check(auth()->guard($guards[0] ?? null)->user())) {
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

        return route('code16.sharp.login');
    }
}
