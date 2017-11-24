<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Contracts\Auth\Factory as Auth;

class SharpAuthenticate extends BaseAuthenticate
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    public function __construct(Auth $auth)
    {
        parent::__construct($auth);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($guards);

        } catch (AuthenticationException $e) {
            if($request->wantsJson()) {
                return response()->json(['message' => "Unauthenticated user"], 401);
            }

            return redirect()->guest(route("code16.sharp.login"));
        }

        return $next($request);
    }
}