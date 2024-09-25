<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Enums\SessionStatusLevel;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SharpAuthenticate extends BaseAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if (Gate::has('viewSharp')) {
            if (!Gate::allows('viewSharp')) {
                $this->unauthenticated($request, $guards);
            }
        } elseif ($checkHandler = config('sharp.auth.check_handler')) {
            if (!instanciate($checkHandler)->check(auth()->guard($guards[0] ?? null)->user())) {
                $this->unauthenticated($request, $guards);
            }
        }

        return $next($request);
    }
    
    protected function unauthenticated($request, array $guards)
    {
        session()->flash('status', session()->get('status', __('sharp::errors.401.status_displayed_in_login_page')));
        session()->flash('status_level', SessionStatusLevel::Error->value);
        
        parent::unauthenticated($request, $guards);
    }
    
    protected function redirectTo(Request $request)
    {
        if ($loginPageUrl = sharp()->config()->get('auth.login_page_url')) {
            return $loginPageUrl;
        }

        if (app(SharpImpersonationHandler::class)?->enabled()) {
            return route('code16.sharp.impersonate');
        }

        return route('code16.sharp.login');
    }
}
