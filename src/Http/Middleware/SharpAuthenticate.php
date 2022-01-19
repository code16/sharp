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
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);

            if ($checkHandler = config('sharp.auth.check_handler')) {
                if (! app($checkHandler)->check(auth()->guard($guards[0] ?? null)->user())) {
                    throw new AuthenticationException();
                }
            }
        } catch (AuthenticationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthenticated user'], 401);
            }

            return redirect()->guest(
                config('sharp.auth.login_page_url', route('code16.sharp.login')),
            );
        }

        return $next($request);
    }
}
