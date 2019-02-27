<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;

class SaveEntityListParams
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->saveQuerystring($request);

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    protected function saveQuerystring($request)
    {
        session()->put($this->getCacheKey($request), $request->getQueryString());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function getCacheKey($request): string
    {
        return "entity-list-qs-{$request->segment(4)}";
    }
}