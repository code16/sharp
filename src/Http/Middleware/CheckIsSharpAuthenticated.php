<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Exceptions\Auth\SharpAuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class CheckIsSharpAuthenticated
{

    /**
     * The authentication factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     * @throws SharpAuthenticationException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(!$this->auth->guard($this->getSharpGuard())->check()) {
            if($request->wantsJson()) {
                throw new SharpAuthenticationException("Unauthenticated user");
            }

            return redirect()->route("code16.sharp.login");
        }

        return $next($request);
    }

    protected function getSharpGuard()
    {
        return config("sharp.auth.guard", config("auth.defaults.guard"));
    }
}