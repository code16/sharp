<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;

class RestoreEntityListParams
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->has("restore-context")) {
            return redirect()->to($request->url() . $this->restoreQuerystring($request));
        }

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function restoreQuerystring($request)
    {
        $querystring = session()->pull($this->getCacheKey($request));

        return $querystring ? "?$querystring" : "";
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function getCacheKey($request): string
    {
        return "entity-list-qs-{$request->segment(3)}";
    }
}