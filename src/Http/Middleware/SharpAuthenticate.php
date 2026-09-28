<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Enums\SessionStatusLevel;
use Code16\Sharp\Exceptions\SharpAuthenticationException;
use Code16\Sharp\Exceptions\SharpTokenMismatchException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $this->unauthorized($request, $guards);
            }
        } elseif ($checkHandler = config('sharp.auth.check_handler')) {
            if (! instanciate($checkHandler)->check(auth()->guard($guards[0] ?? null)->user())) {
                $this->unauthorized($request, $guards);
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

    protected function unauthorized($request, array $guards)
    {
        Auth::guard($guards[0] ?? sharp()->config()->get('auth.guard'))->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('status', __('sharp::auth.access_denied'));
        session()->flash('status_level', SessionStatusLevel::Error->value);

        throw new SharpAuthenticationException(
            'Unauthorized.',
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
