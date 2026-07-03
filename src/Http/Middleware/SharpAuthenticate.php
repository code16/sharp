<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Exceptions\SharpAuthenticationException;
use Code16\Sharp\Exceptions\SharpTokenMismatchException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SharpAuthenticate extends BaseAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) && sharp()->config()->get('auth.guard')
            ? [sharp()->config()->get('auth.guard')]
            : $guards;

        $this->authenticate($request, $guards);

        if (Gate::has('viewSharp')) {
            if (! Gate::allows('viewSharp')) {
                $this->unauthenticated($request, $guards);
            }
        }

        return $next($request);
    }

    protected function unauthenticated($request, array $guards)
    {
        /** reflash status flashed in @see SharpTokenMismatchException::render */
        session()->reflash();

        throw new SharpAuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo($request)
        );
    }

    protected function redirectTo(Request $request)
    {
        if (app(SharpImpersonationHandler::class)?->enabled()) {
            return route('code16.sharp.impersonate');
        }

        return route('code16.sharp.login');
    }
}
