<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;

class SharpAuthenticateOrInMultiFactor extends SharpAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $multiFactor = app(MultiFactorManager::class);

        if ($multiFactor->isEnabled() && $multiFactor->currentHandler()?->isExpectingLogin()) {
            return $next($request);
        }

        return parent::handle($request, $next, ...$guards);
    }
}
