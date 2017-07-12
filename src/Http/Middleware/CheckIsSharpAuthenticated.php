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
        if(!$this->userIsAllowedToUseSharp()) {
            $this->auth->guard($this->getSharpGuard())->logout();

            if($request->wantsJson()) {
                throw new SharpAuthenticationException("Unauthenticated user");
            }

            return redirect()->guest(route("code16.sharp.login"));
        }

        return $next($request);
    }

    protected function getSharpGuard()
    {
        return config("sharp.auth.guard", config("auth.defaults.guard"));
    }

    protected function getSharpAuthCheck()
    {
        return config("sharp.auth.check") ? app(config("sharp.auth.check")) : null;
    }

    protected function userIsAllowedToUseSharp()
    {
        $guard = $this->auth->guard($this->getSharpGuard());

        if($guard->check()) {
            $check = $this->getSharpAuthCheck();

            return is_null($check) || $check->allowUserInSharp($guard->user());
        }

        return false;
    }
}