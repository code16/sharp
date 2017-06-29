<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class CheckIsSharpGuest
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($this->auth->guard($this->getSharpGuard())->check()) {
            return redirect("/");
        }

        return $next($request);
    }

    protected function getSharpGuard()
    {
        return config("sharp.auth.guard", config("auth.defaults.guard"));
    }
}